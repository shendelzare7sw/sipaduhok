@extends('layouts.sneat')

@section('title', 'Admin - Pengaturan Kenaikan Kelas')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik / Promotion /</span> Settings</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Konfigurasi Kenaikan Kelas</h5>
                <div class="card-body">
                    <form action="{{ route('admin.akademik.promotion.settings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $tahun->tahun_ajaran }} ({{ $tahun->semester }})" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pembagian Rapor</label>
                            <input type="date" name="tanggal_pengambilan_rapor" class="form-control" value="{{ $setting->tanggal_pengambilan_rapor ?? '' }}" required>
                            <div class="form-text">Tanggal resmi pembagianapor kepada siswa.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Eksekusi Kenaikan (Otomatis)</label>
                            <input type="date" name="tanggal_eksekusi" class="form-control" value="{{ $setting->tanggal_eksekusi ?? '' }}">
                            <div class="form-text">Jika diisi, sistem akan menjalankan job kenaikan otomatis pada tanggal ini. Kosongkan jika ingin eksekusi manual via tombol.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Persentase Minimal Tuntas (%)</label>
                            <input type="number" name="persentase_minimal_tuntas" class="form-control" min="0" max="100" value="{{ $setting->persentase_minimal_tuntas ?? 70 }}" required>
                            <div class="form-text">Berapa % mata pelajaran yang harus tuntas (>= KKM) agar siswa dianggap layak naik kelas secara akademik.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Informasi Sistem</h5>
                <div class="card-body">
                    <p>Sistem Kenaikan Kelas SIPADUHOK menggunakan logika <strong>PromotionService</strong> dengan kriteria:</p>
                    <ul>
                        <li><strong>Akademik:</strong> Siswa harus menuntaskan minimal X% mata pelajaran (sesuai setting).</li>
                        <li><strong>Keuangan:</strong> Siswa harus berstatus LUNAS untuk semua tagihan wajib, atau mendapat Dispensasi.</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Setelah pengaturan disimpan, Pastikan KKM sudah diisi untuk semua mapel di menu <strong>Pengaturan KKM</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
