@extends('layouts.app')

@section('title', 'Rapor Akhir Semester')
@section('page-title', 'Rapor Akhir Semester')
@section('page-subtitle', 'Pencapaian Kompetensi Peserta Didik')


@section('content')
<style>
    .rapor-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 900px;
        margin: 0 auto;
    }
    .rapor-header {
        text-align: center;
        border-bottom: 3px solid #165fac;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .rapor-header h1 {
        color: #165fac;
        font-size: 24px;
        margin-bottom: 10px;
    }
    .info-siswa table {
        width: 100%;
        margin-bottom: 20px;
    }
    .info-siswa td {
        padding: 6px 0;
    }
    .info-siswa td:first-child {
        width: 250px;
        font-weight: 600;
    }
    .tabel-nilai {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .tabel-nilai th {
        background: #165fac;
        color: white;
        padding: 12px 8px;
        font-size: 13px;
        border: 1px solid #ddd;
    }
    .tabel-nilai td {
        padding: 10px 8px;
        border: 1px solid #ddd;
        font-size: 13px;
    }
    .group-header {
        background: #f0f9ff;
        font-weight: bold;
        color: #165fac;
    }
    .catatan-box {
        border: 2px solid #165fac;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        min-height: 100px;
    }
    @media print {
        .btn, nav, .sidebar, .header { display: none !important; }
        body { background: white; }
    }
</style>

<div class="content-card p-3 mb-3">
    <a href="{{ route('siswa.sia.rapor.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Cetak
    </button>
    <a href="{{ route('siswa.sia.rapor.download', $rapor->id) }}" class="btn btn-success">
        <i class="fas fa-download"></i> Download PDF
    </a>
</div>

<div class="rapor-container">
    <!-- Header -->
    <div class="rapor-header">
        <h1>PENCAPAIAN KOMPETENSI PESERTA DIDIK</h1>
        <p style="margin: 5px 0;">Homeschooling House of Knowledge</p>
        <p style="font-size: 13px; color: #666;">
            Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan
        </p>
    </div>

    <!-- Info Siswa -->
    <div class="info-siswa">
        <table>
            <tr>
                <td>Nama Sekolah</td>
                <td>: Homeschooling House of Knowledge</td>
                <td>Kelas / Fase</td>
                <td>: {{ $rapor->kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td>Nama Peserta Didik</td>
                <td>: {{ $siswa->nama_lengkap }}</td>
                <td>Semester</td>
                <td>: {{ ucfirst($rapor->semester) }}</td>
            </tr>
            <tr>
                <td>Nomor Induk</td>
                <td>: {{ $siswa->nis ?? $siswa->nisn }}</td>
                <td>Tahun Ajaran</td>
                <td>: {{ $rapor->tahunAjaran->nama_tahun_ajaran }}</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Nilai Akademik -->
    <table class="tabel-nilai">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Mata Pelajaran</th>
                <th width="80">Nilai</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            <tr class="group-header">
                <td colspan="4">Kelompok A</td>
            </tr>
            @php 
                $no = 1;
                $kelompokA = $rapor->raporNilai->whereIn('mataPelajaran.nama_mapel', [
                    'Pendidikan Agama', 
                    'Pendidikan Pancasila dan Kewarganegaraan',
                    'Bahasa Indonesia',
                    'Ilmu Pengetahuan Alam',
                    'Ilmu Pengetahuan Sosial',
                    'Bahasa Inggris',
                    'Matematika'
                ]);
            @endphp
            @forelse($kelompokA as $nilai)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($nilai->nilai_angka, 0) }}</td>
                <td style="font-size: 12px;">{{ $nilai->deskripsi ?? 'Memahami materi dengan baik' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #999;">Belum ada data nilai Kelompok A</td>
            </tr>
            @endforelse

            <tr class="group-header">
                <td colspan="4">Kelompok B</td>
            </tr>
            @php 
                $kelompokB = $rapor->raporNilai->whereNotIn('mataPelajaran.nama_mapel', [
                    'Pendidikan Agama', 
                    'Pendidikan Pancasila dan Kewarganegaraan',
                    'Bahasa Indonesia',
                    'Ilmu Pengetahuan Alam',
                    'Ilmu Pengetahuan Sosial',
                    'Bahasa Inggris',
                    'Matematika'
                ]);
            @endphp
            @forelse($kelompokB as $nilai)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($nilai->nilai_angka, 0) }}</td>
                <td style="font-size: 12px;">{{ $nilai->deskripsi ?? 'Menunjukkan perkembangan yang baik' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #999;">Belum ada data nilai Kelompok B</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Kegiatan Ekstrakurikuler -->
    <div style="margin: 30px 0;">
        <h4 style="background: #f0f9ff; padding: 10px; color: #165fac; border-left: 4px solid #165fac;">
            Kegiatan Ekstrakurikuler
        </h4>
        <table class="tabel-nilai" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>Kegiatan Ekstra</th>
                    <th width="100">Predikat</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Life Skill</td>
                    <td style="text-align: center;"><strong>B</strong></td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>Live In</td>
                    <td style="text-align: center;"><strong>B</strong></td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>Menggambar</td>
                    <td style="text-align: center;"><strong>B</strong></td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td>Karate</td>
                    <td style="text-align: center;"><strong>B</strong></td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">5</td>
                    <td>Pengembangan Karakter</td>
                    <td style="text-align: center;"><strong>B</strong></td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td style="text-align: center;">6</td>
                    <td>Literasi</td>
                    <td style="text-align: center;"><strong>B</strong></td>
                    <td>Baik</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Ketidakhadiran -->
    <div style="margin: 30px 0;">
        <h4 style="background: #f0f9ff; padding: 10px; color: #165fac; border-left: 4px solid #165fac;">
            E. KETIDAKHADIRAN
        </h4>
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td style="padding: 8px 0; width: 300px;">Sakit</td>
                <td style="padding: 8px 0;">: <strong>{{ $rapor->jumlah_sakit }}</strong> hari</td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Ijin</td>
                <td style="padding: 8px 0;">: <strong>{{ $rapor->jumlah_izin }}</strong> hari</td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Tanpa Keterangan</td>
                <td style="padding: 8px 0;">: <strong>{{ $rapor->jumlah_alpha }}</strong> hari</td>
            </tr>
        </table>
    </div>

    <!-- Catatan Wali Kelas -->
    <div style="margin: 30px 0;">
        <h4 style="background: #f0f9ff; padding: 10px; color: #165fac; border-left: 4px solid #165fac;">
            F. CATATAN WALI KELAS
        </h4>
        <div class="catatan-box">
            {{ $rapor->catatan_wali_kelas ?? 'Terus tingkatkan semangat belajar dan prestasi.' }}
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div style="margin-top: 60px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p style="margin-bottom: 5px;">Mengetahui,</p>
                    <p style="margin-bottom: 80px;">Wali Siswa Siswa</p>
                    <div style="border-top: 1px solid #333; display: inline-block; width: 200px; margin-bottom: 5px;"></div>
                    <p>(.......................)</p>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <p style="margin-bottom: 5px;">Tangerang Selatan,</p>
                    <p style="margin-bottom: 80px;">Wali Kelas</p>
                    <div style="border-top: 1px solid #333; display: inline-block; width: 200px; margin-bottom: 5px;"></div>
                    <p><strong>{{ $rapor->kelas->waliKelas->nama_lengkap ?? 'nama' }}</strong></p>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 40px;">
            <p style="margin-bottom: 5px;">Mengetahui,</p>
            <p style="margin-bottom: 80px;">Ketua PKBM House Of Knowledge</p>
            <div style="border-top: 1px solid #333; display: inline-block; width: 250px; margin-bottom: 5px;"></div>
            <p><strong>Fransisda Tiodora Ferdiansyah, S.Psi., MM</strong></p>
        </div>
    </div>
</div>

@endsection