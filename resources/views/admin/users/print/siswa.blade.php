<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Siswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; background: #fff; }
        .container { max-width: 297mm; /* Landscape A4 approx */ margin: 0 auto; padding: 10mm; }
        
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
        table th, table td { border: 1px solid #000; padding: 5px 8px; text-align: left; vertical-align: middle; font-size: 10pt; }
        table th { background: #f0f0f0; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        
        /* Footer */
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 200px; display: inline-block; }
        
        /* Print Button */
        @media print {
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; }
        }
        
        .print-controls { position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: #fff; font-family: sans-serif; font-size: 14px; border: none; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .btn-print { background: #3b82f6; }
        .btn-print:hover { background: #2563eb; }
        .btn-back { background: #6b7280; }
        .btn-back:hover { background: #4b5563; }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <a href="{{ route('admin.users.siswa') }}" class="btn btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>LAPORAN DATA SISWA</h3>
            @if(!empty($filterInfo))
                <p>{{ implode(' | ', $filterInfo) }}</p>
            @endif
            <p>Total Data: {{ count($siswa) }} Siswa</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">NIS / NISN</th>
                    <th width="25%">Nama Lengkap</th>
                    <th width="5%">L/P</th>
                    <th width="15%">Kelas (Jenjang)</th>
                    <th width="15%">Cabang</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $index => $s)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $s->nis ?? '-' }} <br>
                            <small>{{ $s->nisn ?? '-' }}</small>
                        </td>
                        <td>{{ $s->nama_lengkap }}</td>
                        <td class="text-center">{{ $s->jenis_kelamin }}</td>
                        <td>
                            {{ $s->kelas->nama_kelas ?? '-' }}
                            @if(isset($s->kelas->jenjang))
                                <br><small>({{ $s->kelas->jenjang }})</small>
                            @endif
                        </td>
                        <td>{{ $s->cabang->nama_cabang ?? '-' }}</td>
                        <td class="text-center">{{ ucfirst($s->status) }}</td>
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
</body>
</html>
