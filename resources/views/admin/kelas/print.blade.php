<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kelas - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/kelas/print.css') }}">
    <script src="{{ asset('js/admin/kelas/print.js') }}" defer></script>
    @include('partials.print-head')
</head>
<body>
    <a href="{{ route('admin.kelas.index') }}" class="back-button no-print">
        &larr; Kembali
    </a>
    <button type="button" id="printButton" class="print-button no-print">
        Cetak
    </button>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR KELAS</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun Ajaran' }}</p>
        </div>

        @if($kelas->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Jenjang</th>
                            <th>Cabang</th>
                            <th>Wali Kelas</th>
                            <th class="col-small">Siswa</th>
                            <th class="col-small">Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelas as $index => $k)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $k->kode_kelas }}</td>
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td class="center">{{ $k->jenjang }}</td>
                            <td>{{ $k->cabang->nama_cabang ?? '-' }}</td>
                            <td>{{ $k->waliKelas->nama_lengkap ?? '-' }}</td>
                            <td class="center">{{ $k->siswa_count }}</td>
                            <td class="center">{{ $k->kuota_siswa }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Kelas</div>
                        <div class="value">{{ $kelas->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Siswa</div>
                        <div class="value">{{ $kelas->sum('siswa_count') }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Kuota</div>
                        <div class="value">{{ $kelas->sum('kuota_siswa') }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas PAUD</div>
                        <div class="value">{{ $kelas->where('jenjang', 'PAUD')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas SD</div>
                        <div class="value">{{ $kelas->where('jenjang', 'SD')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas SMP</div>
                        <div class="value">{{ $kelas->where('jenjang', 'SMP')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas SMA</div>
                        <div class="value">{{ $kelas->where('jenjang', 'SMA')->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p class="empty-message">
                Tidak ada data kelas untuk ditampilkan.
            </p>
        @endif

        <div class="footer">
            <div class="footer-left">
                <p class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
            </div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
                <p><strong>(_________________________)</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
