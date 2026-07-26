<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Wali Siswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/admin/users/print.css'])
</head>
<body>
    <div class="print-controls no-print">
        <a href="{{ route('admin.users.wali-siswa') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        <div class="zoom-controls">
            <button class="btn-zoom" data-zoom-out title="Perkecil">-</button>
            <span class="zoom-level" id="zoomLevel">100%</span>
            <button class="btn-zoom" data-zoom-in title="Perbesar">+</button>
            <button class="btn-zoom btn-fit" data-zoom-reset title="Reset">Fit</button>
        </div>
        <button type="button" class="btn btn-print" data-print-page><i class="fas fa-print"></i> Cetak</button>
    </div>

    <div class="print-wrapper">
        <div class="container">
            @include('partials.print-header', ['cabang' => $cabang ?? null])

            <div class="title">
                <h3>LAPORAN DATA WALI SISWA</h3>
                @if(!empty($filterInfo))
                    <p>{{ implode(' | ', $filterInfo) }}</p>
                @endif
                <p>Total Data: {{ count($orangTua) }} Wali Siswa</p>
            </div>

            <table class="table-top">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Wali Siswa</th>
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
                                    <ul class="child-list">
                                        @foreach($ortu->studentParents as $sp)
                                            <li>
                                                {{ $sp->siswa?->nama_lengkap ?? '-' }}
                                                @if($sp->siswa && $sp->siswa->kelas)
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
                <div class="signature-block">
                    <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                    <p>Mengetahui,</p>
                    <div class="signature-line"></div>
                    <p>Kepala PKBM House of Knowledge</p>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/admin/users/print.js'])
</body>
</html>
