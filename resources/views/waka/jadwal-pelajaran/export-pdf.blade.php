<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : '' }}</title>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 15mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #555;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #333;
            color: #fff;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 10px;
        }

        table td {
            padding: 6px;
            border: 1px solid #666;
            vertical-align: top;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }

        .footer p {
            margin-bottom: 50px;
        }

        .signature {
            margin-top: 10px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
            min-width: 200px;
            text-align: center;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #c82333;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-senin { background: #e3f2fd; color: #1565c0; }
        .badge-selasa { background: #f3e5f5; color: #6a1b9a; }
        .badge-rabu { background: #e8f5e9; color: #2e7d32; }
        .badge-kamis { background: #fff3e0; color: #e65100; }
        .badge-jumat { background: #fce4ec; color: #c2185b; }
        .badge-sabtu { background: #f1f8e9; color: #558b2f; }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>

    <div class="header">
        <h1>Jadwal Pelajaran</h1>
        <h2>{{ $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : 'Semua Tahun Ajaran' }}</h2>
        @if($filterInfo['cabang'] || $filterInfo['jenjang'] || $filterInfo['kelas'] || $filterInfo['guru'])
        <p style="font-size: 11px; color: #333; margin-top: 5px; font-weight: bold;">
            Filter:
            @if($filterInfo['cabang'])
                Cabang: {{ $filterInfo['cabang'] }}
            @endif
            @if($filterInfo['jenjang'])
                {{ $filterInfo['cabang'] ? ' | ' : '' }}Jenjang: {{ $filterInfo['jenjang'] }}
            @endif
            @if($filterInfo['kelas'])
                {{ ($filterInfo['cabang'] || $filterInfo['jenjang']) ? ' | ' : '' }}Kelas: {{ $filterInfo['kelas'] }}
            @endif
            @if($filterInfo['guru'])
                {{ ($filterInfo['cabang'] || $filterInfo['jenjang'] || $filterInfo['kelas']) ? ' | ' : '' }}Guru: {{ $filterInfo['guru'] }}
            @endif
        </p>
        @endif
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y HH:mm') }} WIB</p>
    </div>

    @if($jadwalList->isEmpty())
        <div style="text-align: center; padding: 40px; color: #999;">
            <p style="font-size: 14px;">Tidak ada data jadwal pelajaran</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th style="width: 80px;">Kelas</th>
                    <th style="width: 150px;">Cabang</th>
                    <th style="width: 70px;">Hari</th>
                    <th class="text-center" style="width: 60px;">Jam Mulai</th>
                    <th class="text-center" style="width: 60px;">Jam Selesai</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 130px;">Guru Pengajar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwalList as $index => $jadwal)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $jadwal->kelas->nama_kelas }}</strong></td>
                    <td style="font-size: 9px;">{{ $jadwal->kelas->cabang->nama_cabang }}</td>
                    <td>
                        @php
                            $hariClass = [
                                'Senin' => 'badge-senin',
                                'Selasa' => 'badge-selasa',
                                'Rabu' => 'badge-rabu',
                                'Kamis' => 'badge-kamis',
                                'Jumat' => 'badge-jumat',
                                'Sabtu' => 'badge-sabtu',
                            ][$jadwal->hari] ?? '';
                        @endphp
                        <span class="badge {{ $hariClass }}">{{ $jadwal->hari }}</span>
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                    <td><strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong></td>
                    <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p>Kepala Sekolah / Penanggung Jawab,</p>
            <div class="signature">
                <strong>(__________________________)</strong>
            </div>
        </div>
    @endif

    <script>
        // Auto print on load (optional - user can also use the button)
        window.onload = function() {
            // Uncomment line below to auto-print on page load
            // setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
