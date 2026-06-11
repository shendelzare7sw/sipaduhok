<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa {{ $kelas ? '- Kelas ' . $kelas->nama_kelas : '' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/manajemen-siswa/print.css') }}?v={{ filemtime(public_path('css/admin/manajemen-siswa/print.css')) }}">
    <script src="{{ asset('js/admin/manajemen-siswa/print.js') }}?v={{ filemtime(public_path('js/admin/manajemen-siswa/print.js')) }}" defer></script>
</head>
<body>
    <a href="{{ route('admin.manajemen-siswa.index') }}" class="back-button no-print">&larr; Kembali</a>
    <button type="button" class="print-button no-print" data-print-button>
        <i class="fas fa-print"></i> Cetak
    </button>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR SISWA</h3>
            <p>
                @if($kelas)
                    Kelas: {{ $kelas->nama_kelas }} | Jenjang: {{ $kelas->jenjang }}
                @elseif($cabang)
                    Cabang: {{ $cabang->nama_cabang }}
                @else
                    Semua Siswa
                @endif
                @if($sortBy == 'kelas')
                    | Urut: Per Kelas
                @elseif($sortBy == 'cabang')
                    | Urut: Per Cabang
                @else
                    | Urut: Abjad
                @endif
            </p>
        </div>

        @if($siswaList->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th class="col-number">No</th>
                            <th>NISN</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th class="col-jk">JK</th>
                            <th>Tempat, Tgl Lahir</th>
                            @if(!$kelas)
                                <th>Kelas</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $currentKelas = '';
                        @endphp
                        @foreach($siswaList as $siswa)
                            @if($sortBy == 'kelas' && !$kelas && $currentKelas !== ($siswa->kelas->nama_kelas ?? 'Tanpa Kelas'))
                                @php $currentKelas = $siswa->kelas->nama_kelas ?? 'Tanpa Kelas'; @endphp
                                <tr class="kelas-group-row">
                                    <td colspan="{{ $kelas ? 6 : 7 }}" class="kelas-group-cell">
                                        {{ $currentKelas }} {{ $siswa->kelas ? '(' . $siswa->kelas->jenjang . ')' : '' }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="center">{{ $no++ }}</td>
                                <td>{{ $siswa->nisn }}</td>
                                <td>{{ $siswa->nis ?? '-' }}</td>
                                <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                                <td class="center">{{ $siswa->jenis_kelamin }}</td>
                                <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</td>
                                @if(!$kelas)
                                    <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Siswa</div>
                        <div class="value">{{ $siswaList->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Laki-laki</div>
                        <div class="value">{{ $siswaList->where('jenis_kelamin', 'L')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Perempuan</div>
                        <div class="value">{{ $siswaList->where('jenis_kelamin', 'P')->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p class="empty-print-state">Tidak ada data siswa.</p>
        @endif

        <div class="footer">
            <div class="footer-left">
                <p class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
            </div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                @if($kelas && $kelas->waliKelas)
                    <p>Wali Kelas {{ $kelas->nama_kelas }}</p>
                    <div class="signature-line"></div>
                    <p><strong>{{ $kelas->waliKelas->nama_lengkap }}</strong></p>
                @else
                    <p>Kepala PKBM House of Knowledge</p>
                    <div class="signature-line"></div>
                    <p><strong>(_________________________)</strong></p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
