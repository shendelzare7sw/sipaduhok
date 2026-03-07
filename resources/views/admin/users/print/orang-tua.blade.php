<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Orang Tua</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; background: #fff; }
        .container { width: 297mm; margin: 0 auto; padding: 10mm; }

        /* Header */
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 16pt; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .header h2 { font-size: 14pt; font-weight: bold; margin-bottom: 10px; }
        .header p { font-size: 10pt; color: #333; }

        /* Title */
        .title { text-align: center; margin: 20px 0; }
        .title h3 { font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .title p { font-size: 11pt; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #000; padding: 5px 8px; text-align: left; vertical-align: top; font-size: 10pt; }
        table th { background: #f0f0f0; font-weight: bold; text-align: center; vertical-align: middle; }
        .text-center { text-align: center; }

        /* Footer */
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 200px; display: inline-block; }

        /* Print controls */
        @media print {
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; height: auto !important; }
            .print-wrapper { transform: none !important; margin-top: 0 !important; }
        }

        .print-controls {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            display: flex; justify-content: flex-end; align-items: center;
            gap: 8px; padding: 10px 16px;
            background: rgba(255,255,255,0.97);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; border-radius: 6px; text-decoration: none;
            color: #fff; font-family: sans-serif; font-size: 13px;
            border: none; cursor: pointer; font-weight: 500;
            box-shadow: 0 2px 5px rgba(0,0,0,0.18);
            white-space: nowrap;
        }
        .btn-print { background: #3b82f6; }
        .btn-print:hover { background: #2563eb; }
        .btn-back { background: #6b7280; }
        .btn-back:hover { background: #4b5563; }
        .btn-zoom {
            padding: 6px 10px; background: #f3f4f6; color: #374151;
            border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;
            font-size: 14px; font-weight: 600; display: inline-flex;
            align-items: center; justify-content: center; min-width: 32px;
        }
        .btn-zoom:hover { background: #e5e7eb; }
        .zoom-level { font-size: 12px; font-weight: 600; color: #6b7280; min-width: 40px; text-align: center; }
        .zoom-controls {
            display: inline-flex; align-items: center; gap: 3px;
            background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 2px 3px;
        }
        @media (max-width: 600px) {
            .print-controls { justify-content: center; flex-wrap: wrap; padding: 8px 10px; }
            .btn { font-size: 12px; padding: 7px 14px; }
        }
        .print-wrapper { transform-origin: top left; margin-top: 60px; }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <a href="{{ route('admin.users.orang-tua') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        <div class="zoom-controls">
            <button class="btn-zoom" onclick="zoomOut()" title="Perkecil">-</button>
            <span class="zoom-level" id="zoomLevel">100%</span>
            <button class="btn-zoom" onclick="zoomIn()" title="Perbesar">+</button>
            <button class="btn-zoom" onclick="zoomReset()" title="Reset" style="font-size: 11px;">Fit</button>
        </div>
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak</button>
    </div>

    <div class="print-wrapper">
    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>LAPORAN DATA ORANG TUA</h3>
            @if(!empty($filterInfo))
                <p>{{ implode(' | ', $filterInfo) }}</p>
            @endif
            <p>Total Data: {{ count($orangTua) }} Orang Tua</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Orang Tua</th>
                    <th width="20%">Username / Email</th>
                    <th width="40%">Anak (Siswa)</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orangTua as $index => $ortu)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $ortu->name }}</td>
                        <td>
                            {{ $ortu->username }} <br>
                            <small>{{ $ortu->email }}</small>
                        </td>
                        <td>
                            @if($ortu->studentParents->count() > 0)
                                <ul style="margin: 0; padding-left: 15px;">
                                @foreach($ortu->studentParents as $sp)
                                    <li>
                                        {{ $sp->siswa->nama_lengkap }} 
                                        @if($sp->siswa->kelas)
                                            <small>({{ $sp->siswa->kelas->nama_kelas }})</small>
                                        @endif
                                    </li>
                                @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ $ortu->is_active ? 'Aktif' : 'Non-Aktif' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div>
                <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
                <p>Oleh: {{ auth()->user()->name }}</p>
            </div>
            <div style="text-align: center;">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                <p>Mengetahui,</p>
                <div class="signature-line"></div>
                <p>Kepala PKBM House of Knowledge</p>
            </div>
        </div>
    </div>
    </div>

    <script>
    (function() {
        var currentScale = 1, fitScale = 1, manualZoom = false;
        function applyScale(s) {
            var w = document.querySelector('.print-wrapper'); if (!w) return;
            currentScale = s;
            w.style.transform = 'scale(' + s + ')';
            w.style.transformOrigin = 'top left';
            document.body.style.height = Math.ceil(w.scrollHeight * s + 60) + 'px';
            var l = document.getElementById('zoomLevel'); if (l) l.textContent = Math.round(s * 100) + '%';
        }
        function fit() {
            var w = document.querySelector('.print-wrapper'); if (!w) return;
            w.style.transform = 'none';
            var vw = document.documentElement.clientWidth || window.innerWidth;
            var dw = w.scrollWidth;
            fitScale = vw < dw ? (vw - 4) / dw : 1;
            if (!manualZoom) applyScale(fitScale);
        }
        window.zoomIn = function() { manualZoom = true; applyScale(Math.min(currentScale + 0.1, 2)); };
        window.zoomOut = function() { manualZoom = true; applyScale(Math.max(currentScale - 0.1, 0.3)); };
        window.zoomReset = function() { manualZoom = false; fit(); };
        window.addEventListener('load', fit);
        window.addEventListener('resize', function() { if (!manualZoom) fit(); });
        window.addEventListener('beforeprint', function() {
            var w = document.querySelector('.print-wrapper'); if (w) { w.style.transform = 'none'; }
        });
        window.addEventListener('afterprint', function() { if (manualZoom) applyScale(currentScale); else fit(); });
    })();
    </script>
</body>
</html>
