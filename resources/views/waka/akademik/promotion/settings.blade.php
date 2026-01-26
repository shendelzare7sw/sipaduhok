@extends('layouts.sneat')

@section('title', 'Pegaturan Naik Kelas')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik /</span> Pengaturan Naik Kelas</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Konfigurasi Umum</h5>
                <div class="card-body">
                    <form action="{{ route('waka.promotion.settings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $tahun->nama_tahun_ajaran }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengambilan Rapor</label>
                            <input type="date" name="tanggal_pengambilan_rapor" class="form-control" 
                                   value="{{ $setting->tanggal_pengambilan_rapor ?? '' }}" required>
                            <div class="form-text">Tanggal resmi pembagian rapor.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Eksekusi Naik Kelas Otomatis</label>
                            <input type="date" name="tanggal_eksekusi" class="form-control" 
                                   value="{{ $setting->tanggal_eksekusi ?? '' }}">
                            <div class="form-text">Biarkan kosong jika ingin eksekusi manual saja. Jika diisi, sistem akan memproses otomatis pada tanggal ini.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Persentase Minimal Tuntas (%)</label>
                            <input type="number" name="persentase_minimal_tuntas" class="form-control" 
                                   value="{{ $setting->persentase_minimal_tuntas ?? 70 }}" min="0" max="100" required>
                            <div class="form-text">Siswa dinyatakan lulus akademik jika jumlah mapel tuntas >= persentase ini (dari total mapel).</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Informasi</h5>
                <div class="card-body">
                    <p>Sistem kenaikan kelas otomatis akan memeriksa dua syarat utama:</p>
                    <ol>
                        <li><strong>Syarat Keuangan:</strong> Status Tagihan LUNAS (atau memiliki Izin Khusus dari Ketua PKBM).</li>
                        <li><strong>Syarat Akademik:</strong> Persentase mata pelajaran yang nilainya >= KKM harus memenuhi ambang batas yang ditentukan di samping.</li>
                    </ol>
                    <div class="alert alert-info">
                        <strong>Catatan:</strong> Eksekusi otomatis disarankan dilakukan setelah semua nilai masuk dan divalidasi oleh Wali Kelas.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
