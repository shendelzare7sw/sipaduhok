@extends('layouts.app')

@section('title', 'Rapor Tengah Semester')
@section('page-title', 'Rapor Tengah Semester')
@section('page-subtitle', 'Laporan Penilaian Tengah Semester')


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
        width: 200px;
        font-weight: 600;
    }
    .tabel-nilai {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        table-layout: fixed;
    }
    .tabel-nilai th {
        background: #165fac;
        color: white;
        padding: 12px 8px;
        text-align: center;
        font-size: 13px;
        border: 1px solid #ddd;
    }
    .tabel-nilai td {
        padding: 10px 8px;
        border: 1px solid #ddd;
        text-align: center;
    }
    .tabel-nilai td:nth-child(2) {
        text-align: left;
        padding-left: 12px;
    }
    .ket-tuntas { color: #10b981; font-weight: 600; }
    .ket-tidak-tuntas { color: #ef4444; font-weight: 600; }
    @media print {
        .btn, nav, .sidebar, .header { display: none !important; }
        body { background: white; }
    }
</style>

<div class="content-card mb-3">
    <a href="{{ route('siswa.sia.rapor.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Cetak
    </button>
</div>

<div class="rapor-container">
    <!-- Header -->
    <div class="rapor-header">
        <h1>LAPORAN PENILAIAN TENGAH SEMESTER</h1>
        <p style="margin: 5px 0;">PKBM House of Knowledge</p>
        <p style="font-size: 13px; color: #666;">
            Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan
        </p>
    </div>

    <!-- Info Siswa -->
    <div class="info-siswa">
        <table>
            <tr>
                <td>Nama Siswa</td>
                <td>: {{ $siswa->nama_lengkap }}</td>
                <td>Tahun Ajaran</td>
                <td>: {{ $rapor->tahunAjaran->nama_tahun_ajaran }}</td>
            </tr>
            <tr>
                <td>Nomor Induk</td>
                <td>: {{ $siswa->nis ?? $siswa->nisn }}</td>
                <td>Semester</td>
                <td>: {{ ucfirst($rapor->semester) }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $rapor->kelas->nama_kelas }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Tabel Nilai -->
    <table class="tabel-nilai">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Mata Pelajaran</th>
                <th width="45">KKM</th>
                <th width="45">Tugas</th>
                <th width="40">U1</th>
                <th width="40">U2</th>
                <th width="45">PTS</th>
                <th width="80">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($rapor->raporNilai as $nilai)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                <td>70</td>
                <td>{{ number_format($nilai->nilai_angka * 0.3, 0) }}</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($nilai->nilai_angka, 0) }}</td>
                <td class="{{ $nilai->nilai_angka >= 70 ? 'ket-tuntas' : 'ket-tidak-tuntas' }}">
                    {{ $nilai->nilai_angka >= 70 ? 'Tuntas' : 'Tidak Tuntas' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #999;">
                    Belum ada data nilai
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f9fafb;">
                <td colspan="7" style="text-align: right; font-weight: bold; padding-right: 12px;">
                    Rata-rata:
                </td>
                <td style="font-weight: bold;">{{ number_format($rataRata, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Kegiatan Ekstra -->
    <div style="margin: 30px 0;">
        <h4 style="background: #f0f9ff; padding: 10px; color: #165fac; border-left: 4px solid #165fac;">
            Kegiatan Ekstrakurikuler
        </h4>
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td style="padding: 8px 0; width: 300px;">Seni Musik</td>
                <td style="padding: 8px 0;">: <strong>B</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Menggambar</td>
                <td style="padding: 8px 0;">: <strong>B</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Pengembangan Kepribadian</td>
                <td style="padding: 8px 0;">: <strong>B</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Life Skill</td>
                <td style="padding: 8px 0;">: <strong>B</strong></td>
            </tr>
        </table>
    </div>

    <!-- Kehadiran -->
    <div style="margin: 30px 0;">
        <h4 style="background: #f0f9ff; padding: 10px; color: #165fac; border-left: 4px solid #165fac;">
            Kehadiran
        </h4>
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td style="padding: 8px 0; width: 300px;">Sakit</td>
                <td style="padding: 8px 0;">: <strong>{{ $rapor->jumlah_sakit }}</strong> hari</td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Izin</td>
                <td style="padding: 8px 0;">: <strong>{{ $rapor->jumlah_izin }}</strong> hari</td>
            </tr>
            <tr>
                <td style="padding: 8px 0;">Tanpa Keterangan</td>
                <td style="padding: 8px 0;">: <strong>{{ $rapor->jumlah_alpha }}</strong> hari</td>
            </tr>
        </table>
    </div>

    <!-- Tanda Tangan -->
    <div style="margin-top: 60px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <p style="margin-bottom: 80px;">Wali Siswa</p>
                    <div style="border-top: 1px solid #333; display: inline-block; width: 150px; margin-bottom: 5px;"></div>
                    <p>(.......................)</p>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <p style="margin-bottom: 80px;">Wali Kelas</p>
                    <div style="border-top: 1px solid #333; display: inline-block; width: 150px; margin-bottom: 5px;"></div>
                    <p><strong>{{ $rapor->kelas->waliKelas->nama_lengkap ?? '(....................)' }}</strong></p>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <p style="margin-bottom: 5px;">Pamulang, {{ now()->format('d F Y') }}</p>
                    <p style="margin-bottom: 60px;">Ketua PKBM House of Knowledge</p>
                    <div style="border-top: 1px solid #333; display: inline-block; width: 150px; margin-bottom: 5px;"></div>
                    <p><strong>Fransisda Tiodora Ferdiansyah, S.Psi., MM</strong></p>
                </td>
            </tr>
        </table>
    </div>
</div>

@endsection