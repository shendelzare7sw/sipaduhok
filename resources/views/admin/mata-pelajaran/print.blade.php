<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Pelajaran</title>
    <link rel="stylesheet" href="{{ asset('css/admin/mata-pelajaran/print.css') }}?v={{ filemtime(public_path('css/admin/mata-pelajaran/print.css')) }}">
    <script src="{{ asset('js/admin/mata-pelajaran/print.js') }}?v={{ filemtime(public_path('js/admin/mata-pelajaran/print.js')) }}" defer></script>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-back">&larr; Kembali</a>
        <button type="button" class="btn btn-print" data-print-button>Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR MATA PELAJARAN</h3>
            <p>
                @if(!empty($jenjangFilter))
                    Jenjang: {{ implode(', ', $jenjangFilter) }}
                @else
                    Semua Jenjang
                @endif
            </p>
        </div>

        @if($mataPelajaranList->count() > 0)
            @php
                $currentJenjang = '';
                $no = 1;
            @endphp
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th class="col-number">No</th>
                            <th class="col-code">Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th class="col-jenjang">Jenjang</th>
                            <th class="col-kelompok">Kelompok</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mataPelajaranList as $mapel)
                            @if($currentJenjang !== $mapel->jenjang)
                                @php $currentJenjang = $mapel->jenjang; @endphp
                                <tr class="group-header">
                                    <td colspan="6">Jenjang: {{ $mapel->jenjang }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="center">{{ $no++ }}</td>
                                <td class="center">{{ $mapel->kode_mapel ?? '-' }}</td>
                                <td><strong>{{ $mapel->nama_mapel }}</strong></td>
                                <td class="center">{{ $mapel->jenjang }}</td>
                                <td class="center">{{ $mapel->kelompok ? 'Kel. ' . $mapel->kelompok : '-' }}</td>
                                <td>{{ $mapel->deskripsi ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="summary">
                <h4>Ringkasan Per Jenjang:</h4>
                <div class="summary-grid">
                    @foreach($stats as $jenjang => $total)
                        <div class="summary-item">
                            <div class="label">{{ $jenjang }}</div>
                            <div class="value">{{ $total }}</div>
                        </div>
                    @endforeach
                    <div class="summary-item">
                        <div class="label"><strong>Total Ditampilkan</strong></div>
                        <div class="value">{{ $mataPelajaranList->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p class="empty-print-state">Tidak ada data mata pelajaran ditemukan.</p>
        @endif

        <div class="footer">
            <div class="print-date">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->translatedFormat('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
                <p>(_________________________)</p>
            </div>
        </div>
    </div>
</body>
</html>
