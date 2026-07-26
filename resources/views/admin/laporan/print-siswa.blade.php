<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa {{ $kelas ? '- ' . $kelas->nama_kelas : '' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/laporan/print.css') }}">
@include('partials.print-head')
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-back">&larr; Kembali</a>
        <button type="button" class="btn btn-print btn-print-siswa" data-print-button>Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR SISWA</h3>
            <p>
                @if($kelas) Kelas: {{ $kelas->nama_kelas }} @endif
                @if($cabang) | Cabang: {{ $cabang->nama_cabang }} @endif
                @if($tahunAjaran) | Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran }} @endif
            </p>
        </div>

        @if($siswaList->count() > 0)
            <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th class="col-jk">JK</th>
                        <th>Tempat, Tgl Lahir</th>
                        @if(!$kelas)<th>Kelas</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; $currentGroup = ''; @endphp
                    @foreach($siswaList as $siswa)
                        @if($sortBy == 'kelas' && !$kelas)
                            @php $groupName = $siswa->kelas->nama_kelas ?? 'Tanpa Kelas'; @endphp
                            @if($currentGroup !== $groupName)
                                @php $currentGroup = $groupName; @endphp
                                <tr class="group-header">
                                    <td colspan="{{ $kelas ? 6 : 7 }}">{{ $currentGroup }} {{ $siswa->kelas ? '(' . $siswa->kelas->jenjang . ')' : '' }}</td>
                                </tr>
                            @endif
                        @endif
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td>{{ $siswa->nis ?? '-' }}</td>
                            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                            <td class="center">{{ $siswa->jenis_kelamin }}</td>
                            <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                            @if(!$kelas)<td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>@endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="summary">
                <strong>Ringkasan:</strong>
                <div class="summary-grid">
                    <div class="summary-item"><span class="label">Total:</span> <span class="value">{{ $siswaList->count() }}</span></div>
                    <div class="summary-item"><span class="label">Laki-laki:</span> <span class="value">{{ $siswaList->where('jenis_kelamin', 'L')->count() }}</span></div>
                    <div class="summary-item"><span class="label">Perempuan:</span> <span class="value">{{ $siswaList->where('jenis_kelamin', 'P')->count() }}</span></div>
                </div>
            </div>
        @else
            <p class="empty-message">Tidak ada data siswa.</p>
        @endif

        <div class="footer">
            <div class="print-date">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
                <p><strong>(_________________________)</strong></p>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin/laporan/print.js') }}"></script>
</body>
</html>
