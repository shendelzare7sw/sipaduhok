<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kenaikan Kelas - {{ $selectedYear->nama_tahun_ajaran }}</title>
    @vite([
        'resources/css/admin/akademik/promotion/print.css',
        'resources/js/admin/akademik/promotion/print.js',
    ])
</head>
<body>

    <div class="btn-actions no-print">
        <button type="button" class="btn btn-back" data-history-back>&#8592; Kembali</button>
        <button type="button" class="btn btn-print" data-print-page>&#128438; Cetak</button>
    </div>

    <div class="container">
        {{-- Header --}}
        @php
            $namaSekolah = $cabang ? preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $cabang->nama_cabang) : 'PKBM HOUSE OF KNOWLEDGE';
            $alamat = $cabang ? ($cabang->alamat ?? 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan') : 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan';
            $telepon = $cabang ? ($cabang->telepon ?? '021-7412345') : '021-7412345';
        @endphp
        <div class="header">
            <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK">
            <div class="header-text">
                <h1>{{ $namaSekolah }}</h1>
                <h2>PUSAT KEGIATAN BELAJAR MASYARAKAT</h2>
                <p>{{ $alamat }}</p>
                <p>Telp: {{ $telepon }} | Email: info@hok.sch.id</p>
            </div>
        </div>

        {{-- Title --}}
        <div class="title">
            <h3>LAPORAN KENAIKAN KELAS</h3>
            <p>Tahun Ajaran: {{ $selectedYear->nama_tahun_ajaran }}</p>
            @if($filterStatus)
                <p>Filter Status: <strong>{{ str_replace('_', ' ', $filterStatus) }}</strong></p>
            @endif
        </div>

        <p class="print-meta">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>

        {{-- Statistics --}}
        <div class="stats-grid">
            <div class="stat-box naik">
                <div class="stat-value">{{ $stats['NAIK_KELAS'] ?? 0 }}</div>
                <div class="stat-label">Naik Kelas</div>
            </div>
            <div class="stat-box lulus">
                <div class="stat-value">{{ $stats['LULUS'] ?? 0 }}</div>
                <div class="stat-label">Lulus</div>
            </div>
            <div class="stat-box dispensasi">
                <div class="stat-value">{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</div>
                <div class="stat-label">Naik (Dispensasi)</div>
            </div>
            <div class="stat-box lulus-disp">
                <div class="stat-value">{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</div>
                <div class="stat-label">Lulus (Dispensasi)</div>
            </div>
            <div class="stat-box tidak-naik">
                <div class="stat-value">{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</div>
                <div class="stat-label">Tidak Naik</div>
            </div>
        </div>

        {{-- Student Table --}}
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>Kelas Asal</th>
                    <th>Kelas Tujuan</th>
                    <th>Status Bayar</th>
                    <th>Hasil Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $data)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $data->nama_lengkap }}</td>
                    <td class="center">{{ $data->nis ?? '-' }}</td>
                    <td>{{ $data->kelas_asal }}</td>
                    <td>{{ $data->kelas_tujuan ?? '-' }}</td>
                    <td class="center">
                        @if($data->status_pembayaran == 'LUNAS')
                            Lunas
                        @else
                            Belum Lunas
                        @endif
                    </td>
                    <td class="center">
                        @php
                            $cls = match($data->status_kelulusan) {
                                'NAIK_KELAS' => 'badge-naik',
                                'LULUS' => 'badge-lulus',
                                'NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN' => 'badge-dispensasi',
                                'TIDAK_NAIK_KELAS' => 'badge-tidak-naik',
                                default => ''
                            };
                            $label = match($data->status_kelulusan) {
                                'NAIK_KELAS' => 'Naik Kelas',
                                'LULUS' => 'Lulus',
                                'NAIK_KELAS_TUNGGAKAN' => 'Naik (Disp.)',
                                'LULUS_TUNGGAKAN' => 'Lulus (Disp.)',
                                'TIDAK_NAIK_KELAS' => 'Tidak Naik',
                                default => str_replace('_', ' ', $data->status_kelulusan)
                            };
                        @endphp
                        <span class="{{ $cls }}">{{ $label }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="center">Belum ada data kenaikan kelas yang dieksekusi.</td>
                </tr>
                @endforelse
            </tbody>
            @if($students->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="total-label">Total Siswa:</td>
                    <td class="center total-value">{{ $students->count() }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        {{-- Signature Footer --}}
        <div class="footer">
            <div></div>
            <div class="footer-right">
                <p>{{ now()->format('d F Y') }}</p>
                <br>
                <p>Kepala Sekolah,</p>
                <div class="signature-line"></div>
                <p>(_________________________)</p>
            </div>
        </div>
    </div>

</body>
</html>
