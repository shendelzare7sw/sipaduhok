<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Wali Kelas - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/wali-kelas/print.css') }}?v={{ filemtime(public_path('css/admin/wali-kelas/print.css')) }}">
</head>
<body>
    <a href="{{ route('admin.wali-kelas.index') }}" class="back-button no-print">
        ← Kembali
    </a>
    <button type="button" class="print-button no-print" data-print-page>
        <i class="fas fa-print"></i> Cetak
    </button>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR WALI KELAS</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun Ajaran' }}</p>
        </div>

        @if($kelasList->count() > 0)
            <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Kelas</th>
                        <th>Jenjang</th>
                        <th>Cabang</th>
                        <th>Wali Kelas</th>
                        <th>NIP</th>
                        <th class="col-students">Jml Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentJenjang = ''; $no = 1; @endphp
                    @foreach($kelasList as $kelas)
                        @if($currentJenjang !== $kelas->jenjang)
                            @php $currentJenjang = $kelas->jenjang; @endphp
                            <tr class="jenjang-header">
                                <td colspan="7">{{ $kelas->jenjang }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                            <td class="center">{{ $kelas->jenjang }}</td>
                            <td>{{ $kelas->cabang->nama_cabang ?? '-' }}</td>
                            <td><strong>{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</strong></td>
                            <td>{{ $kelas->waliKelas->nip ?? '-' }}</td>
                            <td class="center">{{ $kelas->siswa_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Kelas dengan Wali</div>
                        <div class="value">{{ $kelasList->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Siswa</div>
                        <div class="value">{{ $kelasList->sum('siswa_count') }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">PAUD</div>
                        <div class="value">{{ $kelasList->where('jenjang', 'PAUD')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">SD</div>
                        <div class="value">{{ $kelasList->where('jenjang', 'SD')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">SMP</div>
                        <div class="value">{{ $kelasList->where('jenjang', 'SMP')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">SMA</div>
                        <div class="value">{{ $kelasList->where('jenjang', 'SMA')->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p class="empty-print-state">
                Tidak ada data wali kelas untuk ditampilkan.
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
    <script src="{{ asset('js/admin/wali-kelas/print.js') }}?v={{ filemtime(public_path('js/admin/wali-kelas/print.js')) }}"></script>
</body>
</html>
