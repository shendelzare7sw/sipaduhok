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

@section('styles')
    @vite(['resources/css/admin/akademik/promotion/settings.css'])
@endsection

@section('content')
@php
    $routePrefix = request()->routeIs('waka.*') ? 'waka.kenaikan-kelas' : 'admin.akademik.kenaikan-kelas';
    $tahunLabel = $tahun->nama_tahun_ajaran ?? $tahun->nama ?? $tahun->tahun_ajaran ?? '-';
    $tanggalRapor = $setting && $setting->tanggal_pengambilan_rapor ? \Carbon\Carbon::parse($setting->tanggal_pengambilan_rapor)->format('Y-m-d') : '';
    $tanggalEksekusi = $setting && $setting->tanggal_eksekusi ? \Carbon\Carbon::parse($setting->tanggal_eksekusi)->format('Y-m-d') : '';
    $waktuEksekusi = $setting && $setting->tanggal_eksekusi ? \Carbon\Carbon::parse($setting->tanggal_eksekusi)->format('H:i') : '02:00';
    $minimalTuntas = $setting->persentase_minimal_tuntas ?? 70;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="promotion-page">
        <div class="page-panel mb-4">
            <div>
                <span class="panel-kicker">Akademik</span>
                <h4 class="panel-title">Pengaturan Kenaikan Kelas</h4>
                <p class="panel-subtitle mb-0">Tetapkan tanggal rapor, jadwal otomatis, dan ambang akademik untuk proses kenaikan kelas.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="content-card">
                    <div class="content-card-header">
                        <div>
                            <h5 class="mb-1">Konfigurasi Utama</h5>
                            <p class="text-muted mb-0">Pengaturan ini dipakai saat simulasi dan eksekusi kenaikan kelas.</p>
                        </div>
                    </div>
                    <div class="content-card-body">
                    <form action="{{ route($routePrefix . '.settings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">

                        <div class="form-block">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $tahunLabel }}" disabled>
                        </div>

                        <div class="form-block">
                            <label class="form-label">Tanggal Pembagian Rapor</label>
                            <input type="date" name="tanggal_pengambilan_rapor" class="form-control"
                                   value="{{ $tanggalRapor }}"
                                   required>
                            <div class="form-text">Tanggal resmi pembagian rapor kepada siswa.</div>
                        </div>

                        <div class="form-block">
                            <label class="form-label">Tanggal Eksekusi Kenaikan (Otomatis)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="date" name="tanggal_eksekusi" class="form-control" 
                                           value="{{ $tanggalEksekusi }}">
                                    <div class="form-text">Tanggal eksekusi</div>
                                </div>
                                <div class="col-md-6">
                                    <input type="time" name="waktu_eksekusi" class="form-control" 
                                           value="{{ $waktuEksekusi }}">
                                    <div class="form-text">Waktu eksekusi (WIB)</div>
                                </div>
                            </div>
                            <div class="form-text mt-2">Jika diisi, sistem akan menjalankan job kenaikan otomatis pada tanggal & waktu ini. Kosongkan jika ingin eksekusi manual via tombol.</div>
                        </div>

                        <div class="form-block">
                            <label class="form-label">Persentase Minimal Tuntas (%)</label>
                            <div class="input-group">
                                <input type="number" name="persentase_minimal_tuntas" class="form-control" min="0" max="100" value="{{ $minimalTuntas }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Berapa % mata pelajaran yang harus tuntas (>= KKM) agar siswa dianggap layak naik kelas secara akademik.</div>
                        </div>

                        <div class="action-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="content-card h-100">
                    <div class="content-card-header">
                        <div>
                            <h5 class="mb-1">Informasi Sistem</h5>
                            <p class="text-muted mb-0">Syarat yang diperiksa saat siswa diproses.</p>
                        </div>
                    </div>
                    <div class="content-card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-icon success"><i class="fas fa-wallet"></i></div>
                                <div>
                                    <h6>Syarat Keuangan</h6>
                                    <p>Status tagihan harus lunas atau memiliki izin khusus dari ketua PKBM.</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon primary"><i class="fas fa-graduation-cap"></i></div>
                                <div>
                                    <h6>Syarat Akademik</h6>
                                    <p>Persentase mata pelajaran yang nilainya memenuhi KKM harus melewati ambang batas.</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon warning"><i class="fas fa-bullseye"></i></div>
                                <div>
                                    <h6>Pastikan KKM Siap</h6>
                                    <p>Lengkapi KKM semua mata pelajaran sebelum menjalankan eksekusi kenaikan kelas.</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route($routePrefix . '.kkm.index') }}" class="btn btn-outline-primary w-100 mt-3">
                            <i class="fas fa-sliders-h me-1"></i> Buka Pengaturan KKM
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
