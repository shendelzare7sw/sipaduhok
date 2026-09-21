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
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; overflow-x: hidden; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .action-buttons { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; justify-content: center; padding: 1rem; }
        .action-btn { display: inline-flex; align-items: center; gap: .5rem; padding: .625rem 1.25rem; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; transition: all .15s; }
        .action-btn-primary { background: #2563eb; color: #fff; }
        .action-btn-primary:hover { background: #1d4ed8; }
        .action-btn-secondary { background: #fff; color: #475569; border: 1px solid #cbd5e1; }
        .action-btn-secondary:hover { background: #f8fafc; }
        .action-btn svg { width: 1.125rem; height: 1.125rem; }
        .zoom-controls { display: inline-flex; align-items: center; gap: .25rem; padding: .25rem; border: 1px solid #cbd5e1; border-radius: .5rem; background: #fff; }
        .zoom-btn { min-width: 2.25rem; height: 2.25rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #cbd5e1; border-radius: .375rem; background: #f8fafc; color: #334155; font-size: .95rem; font-weight: 700; cursor: pointer; }
        .zoom-btn:hover { background: #e2e8f0; }
        .zoom-level { min-width: 3.25rem; color: #64748b; font-size: .75rem; font-weight: 700; text-align: center; user-select: none; }

        .invoice-stage { width: 100%; overflow: auto; padding: 0 1rem 2rem; }
        .invoice-canvas { position: relative; width: 800px; height: auto; margin: 0 auto; }
        .invoice-container { width: 800px; max-width: none; margin: 0; background: #fff; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); position: absolute; top: 0; left: 0; overflow: hidden; transform-origin: top left; }

        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-35deg); font-size: 6rem; font-weight: 800; color: rgba(16,185,129,.08); pointer-events: none; z-index: 0; letter-spacing: .3em; }
        .watermark.pending { color: rgba(245,158,11,.08); }
        .watermark.rejected { color: rgba(239,68,68,.08); }

        .header { display: grid; grid-template-columns: minmax(0, 1fr) 260px; gap: 2rem; align-items: start; padding: 2rem 2rem 1.5rem; border-bottom: 2px solid #e2e8f0; position: relative; z-index: 1; }
        .company-info { display: flex; min-width: 0; align-items: center; gap: 1rem; }
        .company-logo { width: 56px; height: 56px; object-fit: contain; }
        .company-info > div { min-width: 0; }
        .company-info h1 { font-size: 1.125rem; font-weight: 700; color: #0f172a; overflow-wrap: anywhere; }
        .company-info p { font-size: .75rem; color: #64748b; margin-top: .125rem; overflow-wrap: anywhere; }
        .invoice-title { font-size: 1.75rem; font-weight: 800; color: #2563eb; text-align: right; letter-spacing: .05em; }
        .invoice-meta { display: grid; gap: .35rem; margin-top: .5rem; }
        .meta-row { display: grid; grid-template-columns: 78px minmax(0, 1fr); column-gap: .75rem; align-items: start; font-size: .8125rem; }
        .meta-label { color: #94a3b8; text-align: right; white-space: nowrap; }
        .meta-value { min-width: 0; font-weight: 600; color: #334155; overflow-wrap: anywhere; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1.5rem 2rem; position: relative; z-index: 1; }
        .info-box h3 { font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: .5rem; }
        .student-name { font-size: 1rem; font-weight: 700; color: #0f172a; }
        .sub-text { font-size: .8125rem; color: #64748b; margin-top: .125rem; }
        .sub-text.spaced { margin-top: .5rem; }
        .payment-status-box { text-align: right; }
        .status-badge { display: inline-block; padding: .375rem 1rem; border-radius: 9999px; font-size: .75rem; font-weight: 700; }
        .status-disetujui { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-ditolak, .status-gagal { background: #fee2e2; color: #991b1b; }

        .table-container { padding: 0 2rem 1rem; position: relative; z-index: 1; }
        .table-container table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .table-container thead { background: #f8fafc; }
        .table-container th { padding: .75rem 1rem; text-align: left; font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .table-container td { padding: .75rem 1rem; border-bottom: 1px solid #f1f5f9; }
        .table-container td strong { color: #0f172a; }
        .table-container td small { color: #94a3b8; }
        .number-col { width: 2rem; }
        .amount-heading { text-align: right; }
        .amount-col { text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; }
        .table-container th:last-child { text-align: right; }

        .total-section { padding: 0 2rem 1.5rem; position: relative; z-index: 1; }
        .total-box { margin-left: auto; max-width: 320px; }
        .total-row { display: flex; justify-content: space-between; padding: .5rem 0; font-size: .875rem; color: #475569; }
        .grand-total { border-top: 2px solid #e2e8f0; margin-top: .5rem; padding-top: .75rem; font-weight: 800; font-size: 1.125rem; color: #0f172a; }

        .footer { text-align: center; padding: 1.5rem 2rem; border-top: 1px solid #e2e8f0; background: #f8fafc; position: relative; z-index: 1; }
        .footer p { font-size: .8125rem; color: #64748b; }
        .footer .invoice-legal-note { font-size: .6875rem; color: #94a3b8; margin-top: .25rem; font-style: italic; }

        @media print {
            @page { size: A4 portrait; margin: 0; }
            body { background: #fff; }
            .action-buttons { display: none !important; }
            .invoice-stage { padding: 0; overflow: visible; }
            .invoice-canvas { width: 800px !important; height: auto !important; margin: 0; }
            .invoice-container { position: relative; box-shadow: none; margin: 0; border-radius: 0; transform: none !important; }
        }

        @media (max-width: 640px) {
            .action-buttons { padding: .75rem .5rem; }
            .action-btn { padding: .625rem .875rem; }
            .zoom-btn { min-width: 2rem; height: 2rem; }
            .zoom-level { min-width: 2.75rem; }
            .header { grid-template-columns: minmax(0, 1fr); gap: 1rem; }
            .invoice-title { text-align: left; }
            .meta-label { text-align: left; }
            .info-grid { grid-template-columns: 1fr; }
            .payment-status-box { text-align: left; }
        }
    </style>
</head>
<body>
    @php
        $invoiceSubtotal = (int) $items->sum('jumlah_bayar');
        $gatewayTotal = (int) ($pembayaran->gateway_total ?: $invoiceSubtotal);
        $channelFee = max(0, $gatewayTotal - $invoiceSubtotal);
    @endphp

    <div class="action-buttons">
        <button type="button" class="action-btn action-btn-primary" onclick="window.print()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Invoice
        </button>
        <div class="zoom-controls" aria-label="Kontrol ukuran invoice">
            <button type="button" class="zoom-btn" data-invoice-zoom="out" title="Perkecil" aria-label="Perkecil">-</button>
            <span class="zoom-level" id="invoiceZoomLevel">100%</span>
            <button type="button" class="zoom-btn" data-invoice-zoom="in" title="Perbesar" aria-label="Perbesar">+</button>
            <button type="button" class="zoom-btn" data-invoice-zoom="fit" title="Sesuaikan dengan layar" aria-label="Sesuaikan dengan layar">Fit</button>
        </div>
        <button type="button" class="action-btn action-btn-secondary" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="invoice-stage">
        <div class="invoice-canvas" id="invoiceCanvas">
        <div class="invoice-container" id="invoiceContainer">
        {{-- Watermark --}}
        @if($pembayaran->status_validasi == 'disetujui')
            <div class="watermark">PAID</div>
        @elseif($pembayaran->status_validasi == 'pending')
            <div class="watermark pending">PENDING</div>
        @else
            <div class="watermark rejected">{{ strtoupper($pembayaran->payment_status_label) }}</div>
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
            <div>
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
                            @if($pembayaran->metode_pembayaran == 'paywuz')
                                {{ $pembayaran->payment_channel_label }}
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
                <div>
                    <span class="status-badge status-{{ $pembayaran->status_validasi }}">
                        {{ strtoupper($pembayaran->payment_status_label) }}
                    </span>
                </div>
                @if($pembayaran->metode_pembayaran == 'transfer')
                    <p class="sub-text spaced">Bukti Direct Transfer: Terlampir</p>
                @endif
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>Deskripsi Tagihan</th>
                        <th>Tahun Ajaran</th>
                        <th class="amount-heading">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan)) }}</strong>
                            @if($item->keterangan)
                                <br><small>{{ $item->keterangan }}</small>
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
                    <span>Rp {{ number_format($invoiceSubtotal, 0, ',', '.') }}</span>
                </div>
                @if($channelFee > 0)
                    <div class="total-row">
                        <span>Biaya Kanal Pembayaran</span>
                        <span>Rp {{ number_format($channelFee, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="total-row grand-total">
                    <span>TOTAL BAYAR</span>
                    <span>Rp {{ number_format($gatewayTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah melakukan pembayaran tepat waktu.</p>
            <p class="invoice-legal-note">Invoice ini sah dan diproses secara otomatis oleh komputer. Tanda tangan basah tidak diperlukan.</p>
        </div>
    </div>
        </div>
    </div>

    <script>
        (() => {
            const stage = document.querySelector('.invoice-stage');
            const canvas = document.getElementById('invoiceCanvas');
            const invoice = document.getElementById('invoiceContainer');
            const level = document.getElementById('invoiceZoomLevel');
            if (!stage || !canvas || !invoice) return;

            let currentScale = 1;
            let manualZoom = false;

            const applyScale = (scale) => {
                const baseWidth = invoice.offsetWidth;
                const baseHeight = invoice.offsetHeight;
                currentScale = scale;
                invoice.style.transform = `scale(${scale})`;
                canvas.style.width = `${Math.ceil(baseWidth * scale)}px`;
                canvas.style.height = `${Math.ceil(baseHeight * scale)}px`;
                if (level) level.textContent = `${Math.round(scale * 100)}%`;
            };

            const fitToScreen = () => {
                invoice.style.transform = 'none';
                canvas.style.width = `${invoice.offsetWidth}px`;
                canvas.style.height = `${invoice.offsetHeight}px`;
                const availableWidth = Math.max(280, stage.clientWidth - 32);
                const scale = Math.min(1, availableWidth / invoice.offsetWidth);
                applyScale(scale);
            };

            document.querySelectorAll('[data-invoice-zoom]').forEach((button) => {
                button.addEventListener('click', () => {
                    const action = button.dataset.invoiceZoom;
                    if (action === 'fit') {
                        manualZoom = false;
                        fitToScreen();
                        return;
                    }

                    manualZoom = true;
                    applyScale(action === 'in'
                        ? Math.min(currentScale + 0.1, 2)
                        : Math.max(currentScale - 0.1, 0.3));
                });
            });

            window.addEventListener('load', fitToScreen);
            window.addEventListener('resize', () => {
                if (!manualZoom) fitToScreen();
            });

            window.addEventListener('beforeprint', () => {
                invoice.style.transform = 'none';
                invoice.style.position = 'relative';
                canvas.style.width = `${invoice.offsetWidth}px`;
                canvas.style.height = 'auto';
            });

            window.addEventListener('afterprint', () => {
                invoice.style.position = 'absolute';
                if (manualZoom) {
                    applyScale(currentScale);
                } else {
                    fitToScreen();
                }
            });
        })();
    </script>
</body>
</html>
