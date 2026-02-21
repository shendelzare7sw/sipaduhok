<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Akademik Tahunan</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header-table { width: 100%; border-bottom: 3px solid #3b82f6; margin-bottom: 8px; }
        .header-title { text-align: center; margin-bottom: 20px; }
        .header-title h1 { font-size: 18pt; color: #1e40af; margin-bottom: 5px; }
        .header-title p { color: #6b7280; }
        .month-section { margin-bottom: 25px; page-break-inside: avoid; }
        .month-title { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 10px 15px; font-size: 13pt; font-weight: bold; border-radius: 6px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f3f4f6; padding: 10px; text-align: left; font-size: 10pt; border-bottom: 2px solid #d1d5db; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 8pt; font-weight: 600; display: inline-block; }
        .badge-field_trip { background: #cffafe; color: #0e7490; }
        .badge-outing { background: #d1fae5; color: #065f46; }
        .badge-live_in { background: #ede9fe; color: #6b21a8; }
        .badge-hokfest { background: #fed7aa; color: #9a3412; }
        .badge-pts { background: #fee2e2; color: #991b1b; }
        .badge-pas { background: #fecaca; color: #991b1b; }
        .badge-libur { background: #dbeafe; color: #1e40af; }
        .badge-ujian { background: #fce7f3; color: #9f1239; }
        .badge-acara_sekolah { background: #ccfbf1; color: #115e59; }
        .empty-state { text-align: center; padding: 20px; color: #9ca3af; font-style: italic; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 2px solid #e5e7eb; text-align: right; font-size: 9pt; color: #6b7280; }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="75" style="vertical-align: middle; padding-bottom: 10px;">
                <img src="{{ public_path('img/logo/hok-watermark.png') }}" alt="Logo HOK" style="height: 65px; width: auto;">
            </td>
            <td style="text-align: center; vertical-align: middle; padding-bottom: 10px;">
                <div style="font-size: 13pt; font-weight: bold; margin-bottom: 2px;">PKBM HOUSE OF KNOWLEDGE</div>
                <div style="font-size: 9pt; margin-bottom: 2px;">PUSAT KEGIATAN BELAJAR MASYARAKAT</div>
                <div style="font-size: 8pt; color: #555;">Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan</div>
            </td>
            <td width="75"></td>
        </tr>
    </table>
    <div class="header-title">
        <h1>KALENDER AKADEMIK TAHUNAN</h1>
        <p>Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</p>
    </div>

    @if($kegiatanPerBulan->count() > 0)
        @foreach($kegiatanPerBulan as $bulan => $kegiatan)
            @php
                $tanggalBulan = \Carbon\Carbon::parse($bulan . '-01');
            @endphp
            <div class="month-section">
                <div class="month-title">
                    {{ $tanggalBulan->translatedFormat('F Y') }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35%;">Kegiatan</th>
                            <th style="width: 25%;">Tanggal</th>
                            <th style="width: 20%;">Jenis</th>
                            <th style="width: 20%;">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatan as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->nama_kegiatan }}</strong>
                                    @if($event->is_hidden_siswa)
                                        <span class="badge" style="background: #e5e7eb; color: #374151; font-size: 7pt; margin-left: 5px;">Hidden</span>
                                    @endif
                                    @if($event->keterangan)
                                        <br><small style="color: #6b7280;">{{ Str::limit($event->keterangan, 80) }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $event->tanggal_mulai->format('d M Y') }}
                                    @if($event->tanggal_selesai)
                                        <br><small>s.d {{ $event->tanggal_selesai->format('d M Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $event->jenis_kegiatan }}">
                                        {{ $event->jenis_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($event->waktu_mulai)
                                        {{ substr($event->waktu_mulai, 0, 5) }} - {{ substr($event->waktu_selesai, 0, 5) }}
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <p>Belum ada kegiatan terdaftar untuk tahun ajaran ini.</p>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>