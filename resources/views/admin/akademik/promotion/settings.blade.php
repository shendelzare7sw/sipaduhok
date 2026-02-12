@extends('layouts.sneat')

@section('title', 'Admin - Pengaturan Kenaikan Kelas')
@section('page-title', 'Pengaturan Kenaikan Kelas')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header removed, using layout title -->

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Konfigurasi Kenaikan Kelas</h5>
                <div class="card-body">
                    @php
                        $routePrefix = request()->routeIs('waka.*') ? 'waka.promotion' : 'admin.akademik.promotion';
                    @endphp
                    <form action="{{ route($routePrefix . '.settings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $tahun->nama_tahun_ajaran }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pembagian Rapor</label>
                            <input type="date" name="tanggal_pengambilan_rapor" class="form-control" 
                                   value="{{ $setting && $setting->tanggal_pengambilan_rapor ? $setting->tanggal_pengambilan_rapor : '' }}" 
                                   required>
                            <div class="form-text">Tanggal resmi pembagian rapor kepada siswa.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Eksekusi Kenaikan (Otomatis)</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="tanggal_eksekusi" class="form-control" 
                                           value="{{ $setting && $setting->tanggal_eksekusi ? \Carbon\Carbon::parse($setting->tanggal_eksekusi)->format('Y-m-d') : '' }}">
                                    <div class="form-text">Tanggal eksekusi</div>
                                </div>
                                <div class="col-md-6">
                                    <input type="time" name="waktu_eksekusi" class="form-control" 
                                           value="{{ $setting && $setting->tanggal_eksekusi ? \Carbon\Carbon::parse($setting->tanggal_eksekusi)->format('H:i') : '02:00' }}">
                                    <div class="form-text">Waktu eksekusi (WIB)</div>
                                </div>
                            </div>
                            <div class="form-text mt-2">Jika diisi, sistem akan menjalankan job kenaikan otomatis pada tanggal & waktu ini. Kosongkan jika ingin eksekusi manual via tombol.</div>
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
                    <p>Sistem kenaikan kelas otomatis akan memeriksa dua syarat utama:</p>
                    <ol>
                        <li><strong>Syarat Keuangan:</strong> Status Tagihan LUNAS (atau memiliki Izin Khusus dari Ketua PKBM).</li>
                        <li><strong>Syarat Akademik:</strong> Persentase mata pelajaran yang nilainya >= KKM harus memenuhi ambang batas yang ditentukan di samping.</li>
                    </ol>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Setelah pengaturan disimpan, Pastikan KKM sudah diisi untuk semua mapel di menu <strong>Pengaturan KKM</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
