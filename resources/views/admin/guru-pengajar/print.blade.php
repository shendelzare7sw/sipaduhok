<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Guru Pengajar - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/guru-pengajar/print.css') }}">
    @include('partials.print-head')
</head>
<body>
    <a href="{{ route('admin.guru-pengajar.index') }}" class="back-button no-print">&larr; Kembali</a>
    <button type="button" class="print-button no-print" data-print-button><i class="fas fa-print"></i> Cetak</button>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR GURU PENGAJAR</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun Ajaran' }}</p>
        </div>

        @if($guruList->count() > 0)
            <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Telepon</th>
                        <th>Penugasan (Kelas - Mata Pelajaran)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guruList as $index => $guru)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td><strong>{{ $guru->nama_lengkap }}</strong></td>
                        <td>{{ $guru->nip ?? '-' }}</td>
                        <td>{{ $guru->telepon ?? '-' }}</td>
                        <td>
                            @if($guru->guruKelas->count() > 0)
                                <ul class="assignment-list">
                                    @foreach($guru->guruKelas as $assignment)
                                        <li>{{ $assignment->kelas?->nama_kelas ?? '-' }} - {{ $assignment->mataPelajaran?->nama_mapel ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="no-assignment">Belum ada penugasan</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Guru</div>
                        <div class="value">{{ $guruList->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Penugasan</div>
                        <div class="value">{{ $guruList->sum(fn($g) => $g->guruKelas->count()) }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Guru dengan Penugasan</div>
                        <div class="value">{{ $guruList->filter(fn($g) => $g->guruKelas->count() > 0)->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p class="empty-message">Tidak ada data guru pengajar.</p>
        @endif

        <div class="footer">
            <div class="footer-left">
                <p class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
            </div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin/guru-pengajar/print.js') }}"></script>
</body>
</html>
