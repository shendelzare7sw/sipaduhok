<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap Tagihan - {{ $selectedYear->nama_tahun_ajaran ?? '-' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/keuangan/tagihan/cetak-laporan.css') }}?v={{ filemtime(public_path('css/admin/keuangan/tagihan/cetak-laporan.css')) }}">
</head>
<body>
    <div class="no-print print-actions">
        <button type="button" class="print-action-button print-button" data-print-page>
            <i class="fas fa-print"></i> Cetak
        </button>
        <button type="button" class="print-action-button close-button" data-close-page>
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    @include('partials.print-header', ['cabang' => $cabang ?? null])

    <div class="title">
        <h3>LAPORAN REKAP TAGIHAN SISWA</h3>
        <p>Tahun Ajaran: {{ $selectedYear->nama_tahun_ajaran ?? '-' }}</p>
        @if($selectedKelas)
            <p>Kelas: {{ $selectedKelas->nama_kelas }} ({{ $selectedKelas->jenjang }})</p>
        @else
            <p>Kelas: Semua Kelas</p>
        @endif
        @if(!empty($filters['search']))
            <p>Pencarian: "{{ $filters['search'] }}"</p>
        @endif
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Jumlah Siswa</td>
                <td>: {{ $siswaList->count() }} siswa</td>
            </tr>
            <tr>
                <td>Total Tagihan</td>
                <td>: Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td>: Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Tunggakan</td>
                <td>: Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="tagihan-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th>Nama Siswa</th>
                <th class="col-nisn">NISN</th>
                <th class="col-kelas">Kelas</th>
                <th class="col-cabang">Cabang</th>
                <th class="text-right col-money">Total Tagihan</th>
                <th class="text-right col-money">Terbayar</th>
                <th class="text-right col-money">Sisa</th>
                <th class="col-status">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $index => $siswa)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td class="text-center">{{ $siswa->nisn ?? '-' }}</td>
                    <td class="text-center">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $siswa->cabang->nama_cabang ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)
                            <span class="status-lunas">LUNAS</span>
                        @elseif($siswa->total_tagihan == 0)
                            <span class="status-kosong">KOSONG</span>
                        @else
                            <span class="status-belum">BELUM</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center empty-row">
                        Tidak ada data siswa sesuai filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">GRAND TOTAL:</td>
                <td class="text-right">Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td colspan="2"><strong>Ringkasan Tagihan</strong></td>
            </tr>
            <tr>
                <td>Jumlah Siswa</td>
                <td>{{ $siswaList->count() }} siswa</td>
            </tr>
            <tr>
                <td>Siswa Lunas</td>
                <td>{{ $siswaList->filter(fn($s) => $s->sisa_tagihan <= 0 && $s->total_tagihan > 0)->count() }} siswa</td>
            </tr>
            <tr>
                <td>Siswa Belum Lunas</td>
                <td>{{ $siswaList->filter(fn($s) => $s->sisa_tagihan > 0)->count() }} siswa</td>
            </tr>
            <tr class="grand-total">
                <td>Total Tagihan</td>
                <td>Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td>Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Tunggakan</strong></td>
                <td><strong>Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="sign">
            <p>Tangerang Selatan, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Admin / Kepala Sekolah</p>
            <div class="sign-line">
                ( ................................ )
            </div>
        </div>
    </div>

    <div class="print-meta">
        <em>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</em>
    </div>
    <script src="{{ asset('js/admin/keuangan/tagihan/cetak-laporan.js') }}?v={{ filemtime(public_path('js/admin/keuangan/tagihan/cetak-laporan.js')) }}"></script>
</body>
</html>
