<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PAS - {{ $rapor->siswa->nama_lengkap }}</title>
    @vite('resources/css/rapor-document.css')
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/rapor/print-pas.css') }}">
</head>
<body class="font-[Arial,sans-serif] text-[10pt] leading-[1.4]">
    @php
        $alignmentValue = fn($value, $default) => in_array($value, ['left', 'center', 'right', 'justify'], true) ? $value : $default;
        $deskripsiAlignment = $alignmentValue($rapor->deskripsi_alignment ?? null, 'left');
        $keteranganEkstraAlignment = $alignmentValue($rapor->keterangan_ekstra_alignment ?? null, 'left');
        $catatanAlignment = $alignmentValue($rapor->catatan_alignment ?? null, 'center');
        $alignmentClasses = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right', 'justify' => 'text-justify'];
    @endphp

    <!-- Header -->
    <div class="mb-5 text-center">
        <h1 class="my-[5px] text-[16pt]">HOUSE OF KNOWLEDGE</h1>
        <h2 class="my-[5px] text-[14pt] font-normal">The Second Home For Your Children - 家庭教育</h2>
        <p class="my-[3px] text-[9pt]">Komplek Ruko Reni Jaya Baru Jl.Ketapang III Blok AF 5 No 22-23 Pamulang Barat – Tangerang Selatan | Telp. 021 – 7427521 / 085811278144 | Email: hokhomeshool@gmail.com</p>
        <hr class="my-[15px]">
        <h1 class="my-[5px] text-[16pt]">PENCAPAIAN KOMPETENSI PESERTA DIDIK</h1>
    </div>

    <!-- Info Siswa -->
    <table class="mb-[15px] w-full [&_td]:px-2 [&_td]:py-[3px] [&_td:first-child]:w-[150px] [&_td:first-child]:font-bold">
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

    <!-- Kelompok A (Wajib) -->
    <table class="mb-[15px] w-full border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:p-[5px] [&_td:first-child]:w-10 [&_td:first-child]:text-center [&_td:nth-child(3)]:w-[60px] [&_td:nth-child(3)]:text-center [&_th]:border [&_th]:border-solid [&_th]:border-black [&_th]:bg-[#f0f0f0] [&_th]:p-[5px] [&_th]:text-center [&_th]:font-bold">
        <thead>
            <tr class="bg-[#d0d0d0] text-center font-bold">
                <th colspan="4">KELOMPOK A (Wajib)</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $visibleNilai = $rapor->raporNilai->filter(fn($rn) => $rn->is_visible);
            @endphp
            @foreach($visibleNilai as $nilai)
                @php $kel = $nilai->kelompok_override ?? ($nilai->mataPelajaran->kelompok ?? ''); @endphp
                @if(trim($kel) == 'A')
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                    <td>{{ $nilai->nilai_angka }}</td>
                    <td class="{{ $alignmentClasses[$deskripsiAlignment] }}">{{ $nilai->deskripsi ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Kelompok B (Pilihan) -->
    <table class="mb-[15px] w-full border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:p-[5px] [&_td:first-child]:w-10 [&_td:first-child]:text-center [&_td:nth-child(3)]:w-[60px] [&_td:nth-child(3)]:text-center [&_th]:border [&_th]:border-solid [&_th]:border-black [&_th]:bg-[#f0f0f0] [&_th]:p-[5px] [&_th]:text-center [&_th]:font-bold">
        <thead>
            <tr class="bg-[#d0d0d0] text-center font-bold">
                <th colspan="4">KELOMPOK B (Pilihan)</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach($visibleNilai as $nilai)
                @php $kel = $nilai->kelompok_override ?? ($nilai->mataPelajaran->kelompok ?? ''); @endphp
                @if(trim($kel) == 'B')
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                    <td>{{ $nilai->nilai_angka }}</td>
                    <td class="{{ $alignmentClasses[$deskripsiAlignment] }}">{{ $nilai->deskripsi ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Kegiatan Ekstrakurikuler -->
    <table class="mb-[15px] w-full border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:p-[5px] [&_th]:border [&_th]:border-solid [&_th]:border-black [&_th]:bg-[#f0f0f0] [&_th]:p-[5px]">
        <thead>
            <tr class="bg-[#d0d0d0] text-center font-bold">
                <th colspan="4">Kegiatan Ekstrakurikuler</th>
            </tr>
            <tr>
                <th width="40">No</th>
                <th>Kegiatan</th>
                <th width="80">Predikat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rapor->kegiatanEkstra as $ekstra)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $ekstra->kegiatan_nama }}</td>
                <td class="text-center">{{ $ekstra->predikat ?? '-' }}</td>
                <td class="{{ $alignmentClasses[$keteranganEkstraAlignment] }}">{{ $ekstra->keterangan ?? '-' }}</td>
            </tr>
            @empty
            @foreach(\App\Models\RaporKegiatanEkstra::getDefaultKegiatan() as $kegiatan)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $kegiatan }}</td>
                <td class="text-center">-</td>
                <td>-</td>
            </tr>
            @endforeach
            @endforelse
        </tbody>
    </table>

    <!-- Ketidakhadiran -->
    <h3>Ketidakhadiran</h3>
    <table class="mb-[15px] w-1/2 border-collapse [&_td]:border [&_td]:border-solid [&_td]:border-black [&_td]:p-[5px] [&_td:first-child]:w-[150px] [&_td:first-child]:font-bold">
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
    </table>

    <!-- Catatan Wali Kelas -->
    <h3>Catatan Wali Kelas</h3>
    <div class="mb-[15px] min-h-20 border border-solid border-black p-[10px] {{ $alignmentClasses[$catatanAlignment] }}">
        {{ $rapor->catatan_wali_kelas ?? '-' }}
    </div>

    <!-- Tanda Tangan -->
    <div class="mt-[30px]">
        <table class="w-full [&_td]:p-[10px] [&_td]:text-center [&_td]:align-top">
            <tr>
                <td width="33%">
                    <div>Wali Siswa/Wali</div>
                    <div class="mt-[60px] [border-top:1px_solid_#000] pt-[5px]">(...........................)</div>
                </td>
                <td width="34%" class="text-center">
                    <div>{{ $rapor->kelas->cabang->kota ?? 'Tangerang Selatan' }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                </td>
                <td width="33%">
                    <div>Wali Kelas</div>
                    <div class="mt-[60px] [border-top:1px_solid_#000] pt-[5px]">{{ $rapor->kelas->waliKelas->nama ?? '(...........................)' }}</div>
                </td>
            </tr>
        </table>
        <div class="mt-[30px] text-center font-bold">
            Ketua PKBM House of Knowledge<br>
            <div class="mt-[60px] inline-block [border-top:1px_solid_#000] pt-[5px]">
                Fransisda Tiodora Ferdiansyah, S.Psi., MM
            </div>
        </div>
    </div>
</body>
</html>
