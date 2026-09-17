<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PTS - {{ $rapor->siswa->nama_lengkap }}</title>
    @vite('resources/css/rapor-document.css')
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/rapor/print-pts.css') }}">
</head>
<body class="font-[Arial,sans-serif] text-[9pt] leading-[1.18]">
    <!-- Header -->
    <div class="mb-2 text-center">
        <h1 class="my-[3px] text-[12pt]">HOUSE OF KNOWLEDGE</h1>
        <h2 class="my-0.5 text-[10pt] font-normal">The Second Home For Your Children - 家庭教育</h2>
        <p class="my-0.5 text-[7.5pt]">Jl. Contoh No. 123, Kota, Provinsi | Telp: (021) 1234567 | Email: info@hok.sch.id</p>
        <hr class="my-2">
        <h1 class="my-[3px] text-[12pt]">LAPORAN PENILAIAN TENGAH SEMESTER</h1>
    </div>

    <!-- Info Siswa -->
    <table class="mb-[7px] w-full [&_td]:px-[6px] [&_td]:py-px [&_td:first-child]:w-[150px] [&_td:first-child]:font-bold">
        <tr>
            <td>Nama Siswa</td>
            <td>: {{ $rapor->siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Nomor Induk</td>
            <td>: {{ $rapor->siswa->nis }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $rapor->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td>: {{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>: {{ ucfirst($rapor->semester) }}</td>
        </tr>
    </table>

    <!-- Tabel Nilai -->
    <table class="mb-2 w-full table-fixed border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:px-[3px] [&_td]:py-0.5 [&_td]:text-center [&_td]:text-[8pt] [&_td:nth-child(2)]:text-left [&_th]:border [&_th]:border-solid [&_th]:border-black [&_th]:bg-[#f0f0f0] [&_th]:px-[3px] [&_th]:py-0.5 [&_th]:text-center [&_th]:text-[8pt] [&_th]:font-bold">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Mata Pelajaran</th>
                <th width="45">KKM</th>
                <th width="45">Tugas</th>
                <th width="45">U1</th>
                <th width="45">U2</th>
                <th width="45">PTS</th>
                <th width="80">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalNilai = 0;
                $count = 0;
                $visibleNilai = $rapor->raporNilai->filter(fn($rn) => $rn->is_visible);
            @endphp
            @foreach($visibleNilai as $nilai)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel ?? '-' }}</td>
                <td>{{ $nilai->mataPelajaran->kkm ?? 75 }}</td>
                <td>{{ $nilai->nilai->rata_tugas ?? '-' }}</td>
                <td>{{ $nilai->nilai->rata_latihan ?? '-' }}</td>
                <td>{{ $nilai->nilai->rata_uh ?? '-' }}</td>
                <td>{{ $nilai->nilai->pts ?? '-' }}</td>
                <td>{{ ($nilai->nilai->pts ?? 0) >= ($nilai->mataPelajaran->kkm ?? 75) ? 'Tuntas' : 'Tidak Tuntas' }}</td>
            </tr>
            @php
                $totalNilai += $nilai->nilai->pts ?? 0;
                $count++;
            @endphp
            @endforeach
            <tr class="font-bold">
                <td colspan="6">Jumlah</td>
                <td>{{ $totalNilai }}</td>
                <td></td>
            </tr>
            <tr class="font-bold">
                <td colspan="6">Rata-rata</td>
                <td>{{ $count > 0 ? number_format($totalNilai / $count, 2) : 0 }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Kegiatan Ekstrakurikuler -->
    <h3 class="mb-1 mt-[6px] text-[9.5pt]">Kegiatan Ekstrakurikuler</h3>
    <table class="mb-2 w-[60%] border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:px-[3px] [&_td]:py-0.5 [&_th]:border [&_th]:border-solid [&_th]:border-black [&_th]:bg-[#f0f0f0] [&_th]:px-[3px] [&_th]:py-0.5">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Nama Kegiatan</th>
                <th width="80">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rapor->kegiatanEkstra as $ekstra)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $ekstra->kegiatan_nama }}</td>
                <td class="text-center">{{ $ekstra->predikat ?? '-' }}</td>
            </tr>
            @empty
            @foreach(\App\Models\RaporKegiatanEkstra::getDefaultKegiatan() as $kegiatan)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $kegiatan }}</td>
                <td class="text-center">-</td>
            </tr>
            @endforeach
            @endforelse
        </tbody>
    </table>

    <!-- Ketidakhadiran -->
    <h3 class="mb-1 mt-[6px] text-[9.5pt]">Ketidakhadiran</h3>
    <table class="mb-2 w-1/2 border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:px-[3px] [&_td]:py-0.5 [&_td:first-child]:w-[150px] [&_td:first-child]:font-bold">
        <tr>
            <td>Sakit</td>
            <td>: {{ $rapor->jumlah_sakit }} hari</td>
        </tr>
        <tr>
            <td>Izin</td>
            <td>: {{ $rapor->jumlah_izin }} hari</td>
        </tr>
        <tr>
            <td>Tanpa Keterangan</td>
            <td>: {{ $rapor->jumlah_alpha }} hari</td>
        </tr>
        <tr class="font-bold">
            <td>Jumlah</td>
            <td>: {{ $rapor->jumlah_sakit + $rapor->jumlah_izin + $rapor->jumlah_alpha }} hari</td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="mt-3">
        <table class="w-full [&_td]:p-1 [&_td]:text-center [&_td]:align-top">
            <tr>
                <td width="33%">
                    <div>Wali Siswa/Wali</div>
                    <div class="mt-7 [border-top:1px_solid_#000] pt-[3px]">(...........................)</div>
                </td>
                <td width="34%" class="text-center">
                    <div>{{ $rapor->kelas->cabang->kota ?? 'Kota' }}, {{ now()->format('d F Y') }}</div>
                </td>
                <td width="33%">
                    <div>Wali Kelas</div>
                    <div class="mt-7 [border-top:1px_solid_#000] pt-[3px]">{{ $rapor->kelas->waliKelas->nama ?? '(...........................)' }}</div>
                </td>
            </tr>
        </table>
        <div class="mt-[10px] text-right font-bold">
            <div class="mb-[5px]">{{ $rapor->kelas->cabang->kota ?? 'Tangerang Selatan' }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div class="inline-block text-center">
                <div>Ketua PKBM House of Knowledge</div>
                <div class="mt-8 inline-block [border-top:1px_solid_#000] pt-[5px]">
                    Fransisda Tiodora Ferdiansyah, S.Psi., MM
                </div>
            </div>
        </div>
    </div>
</body>
</html>
