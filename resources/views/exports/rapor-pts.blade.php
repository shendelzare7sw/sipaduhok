<table>
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold; font-size: 14pt;">
                LAPORAN PENILAIAN TENGAH SEMESTER
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold;">
                House of Knowledge - The Second Home For Your Children
            </th>
        </tr>
        <tr><th colspan="8"></th></tr>
        <tr>
            <th>Nama Siswa</th>
            <th colspan="7">{{ $rapor->siswa->nama_lengkap }}</th>
        </tr>
        <tr>
            <th>Nomor Induk</th>
            <th colspan="7">{{ $rapor->siswa->nis }}</th>
        </tr>
        <tr>
            <th>Kelas</th>
            <th colspan="7">{{ $rapor->kelas->nama_kelas }}</th>
        </tr>
        <tr>
            <th>Tahun Ajaran</th>
            <th colspan="7">{{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '' }}</th>
        </tr>
        <tr>
            <th>Semester</th>
            <th colspan="7">{{ ucfirst($rapor->semester) }}</th>
        </tr>
        <tr><th colspan="8"></th></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th>No</th>
            <th>Mata Pelajaran</th>
            <th>KKM</th>
            <th>Tugas</th>
            <th>U1</th>
            <th>U2</th>
            <th>PTS</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
            $totalNilai = 0;
            $count = 0;
        @endphp
        @foreach($rapor->raporNilai as $nilai)
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
        <tr style="font-weight: bold;">
            <td colspan="6">Jumlah</td>
            <td>{{ $totalNilai }}</td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <td colspan="6">Rata-rata</td>
            <td>{{ $count > 0 ? number_format($totalNilai / $count, 2) : 0 }}</td>
            <td></td>
        </tr>
        <tr><td colspan="8"></td></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="8">Kegiatan Ekstrakurikuler</th>
        </tr>
        <tr style="font-weight: bold;">
            <th>No</th>
            <th colspan="5">Nama Kegiatan</th>
            <th colspan="2">Predikat</th>
        </tr>
        @forelse($rapor->kegiatanEkstra as $ekstra)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td colspan="5">{{ $ekstra->kegiatan_nama }}</td>
            <td colspan="2">{{ $ekstra->predikat ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align: center;">Tidak ada data kegiatan ekstrakurikuler</td>
        </tr>
        @endforelse
        <tr><td colspan="8"></td></tr>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <th colspan="8">Ketidakhadiran</th>
        </tr>
        <tr>
            <th>Sakit</th>
            <td>{{ $rapor->jumlah_sakit }} hari</td>
            <th>Izin</th>
            <td>{{ $rapor->jumlah_izin }} hari</td>
            <th>Tanpa Keterangan</th>
            <td colspan="3">{{ $rapor->jumlah_alpha }} hari</td>
        </tr>
    </tbody>
</table>
