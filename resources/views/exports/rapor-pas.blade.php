<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 14pt;">
                PENCAPAIAN KOMPETENSI PESERTA DIDIK
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold;">
                House of Knowledge - The Second Home For Your Children
            </th>
        </tr>
        <tr><th colspan="4"></th></tr>
        <tr>
            <th>Nama Siswa</th>
            <th colspan="3">{{ $rapor->siswa->nama_lengkap }}</th>
        </tr>
        <tr>
            <th>Nomor Induk</th>
            <th colspan="3">{{ $rapor->siswa->nis }}</th>
        </tr>
        <tr>
            <th>Kelas</th>
            <th colspan="3">{{ $rapor->kelas->nama_kelas }}</th>
        </tr>
        <tr>
            <th>Tahun Ajaran</th>
            <th colspan="3">{{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '' }}</th>
        </tr>
        <tr>
            <th>Semester</th>
            <th colspan="3">{{ ucfirst($rapor->semester) }}</th>
        </tr>
        <tr><th colspan="4"></th></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="4">KELOMPOK A (Wajib)</th>
        </tr>
        <tr style="font-weight: bold;">
            <th>No</th>
            <th>Mata Pelajaran</th>
            <th>Nilai</th>
            <th>Capaian Kompetensi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $kelompokA = ['Pendidikan Agama', 'PPKn', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris', 'Matematika'];
        @endphp
        @foreach($rapor->raporNilai as $nilai)
            @if(in_array($nilai->mataPelajaran->nama_mapel ?? '', $kelompokA))
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                <td>{{ $nilai->nilai_angka }}</td>
                <td>{{ $nilai->deskripsi ?? '-' }}</td>
            </tr>
            @endif
        @endforeach
        <tr><td colspan="4"></td></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="4">KELOMPOK B (Pilihan)</th>
        </tr>
        <tr style="font-weight: bold;">
            <th>No</th>
            <th>Mata Pelajaran</th>
            <th>Nilai</th>
            <th>Capaian Kompetensi</th>
        </tr>
        @php
            $no = 1;
            $kelompokB = ['TIK', 'PJOK', 'Bahasa Mandarin', 'Musik', 'Tata Boga'];
        @endphp
        @foreach($rapor->raporNilai as $nilai)
            @if(in_array($nilai->mataPelajaran->nama_mapel ?? '', $kelompokB))
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                <td>{{ $nilai->nilai_angka }}</td>
                <td>{{ $nilai->deskripsi ?? '-' }}</td>
            </tr>
            @endif
        @endforeach
        <tr><td colspan="4"></td></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="4">Kegiatan Ekstrakurikuler</th>
        </tr>
        <tr style="font-weight: bold;">
            <th>No</th>
            <th>Kegiatan</th>
            <th>Predikat</th>
            <th>Keterangan</th>
        </tr>
        @forelse($rapor->kegiatanEkstra as $ekstra)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $ekstra->kegiatan_nama }}</td>
            <td>{{ $ekstra->predikat ?? '-' }}</td>
            <td>{{ $ekstra->keterangan ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align: center;">Tidak ada data kegiatan ekstrakurikuler</td>
        </tr>
        @endforelse
        <tr><td colspan="4"></td></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="4">Ketidakhadiran</th>
        </tr>
        <tr>
            <th>Sakit</th>
            <td>{{ $rapor->jumlah_sakit }} hari</td>
            <th>Izin</th>
            <td>{{ $rapor->jumlah_izin }} hari</td>
        </tr>
        <tr>
            <th>Tanpa Keterangan</th>
            <td colspan="3">{{ $rapor->jumlah_alpha }} hari</td>
        </tr>
        <tr><td colspan="4"></td></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="4">Catatan Wali Kelas</th>
        </tr>
        <tr>
            <td colspan="4">{{ $rapor->catatan_wali_kelas ?? '-' }}</td>
        </tr>
    </tbody>
</table>
