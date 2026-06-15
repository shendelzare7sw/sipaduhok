<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Akademik Tahunan</title>
    <link rel="stylesheet" href="{{ public_path('css/admin/akademik/kalender/cetak-tahunan.css') }}">
</head>
<body>
    <div class="header">
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
                            <th class="col-kegiatan">Kegiatan</th>
                            <th class="col-tanggal">Tanggal</th>
                            <th class="col-jenis">Jenis</th>
                            <th class="col-waktu">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatan as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->nama_kegiatan }}</strong>
                                    @if($event->keterangan)
                                        <br><small class="muted-desc">{{ Str::limit($event->keterangan, 80) }}</small>
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
                                        <span class="empty-mark">-</span>
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
