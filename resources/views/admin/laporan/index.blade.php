@extends('layouts.sneat')

@section('title', 'Cetak Laporan')

@section('page-title', 'Cetak Laporan')
@section('page-subtitle', 'Cetak berbagai jenis laporan data akademik')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@php
    $kelasJson = $kelasList->map(function ($kelas) {
        return [
            'id' => $kelas->id,
            'nama_kelas' => $kelas->nama_kelas,
            'jenjang' => $kelas->jenjang,
            'cabang_id' => $kelas->cabang_id,
        ];
    })->values();
@endphp

@section('styles')
    @vite(['resources/css/admin/laporan/index.css'])
@endsection

@section('content')
<div class="admin-laporan-page">
    <div id="laporanKelasData" data-kelas="{{ $kelasJson->toJson() }}"></div>

    <!-- Stats Row -->
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-blue">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalSiswa'] }}</div>
                <div class="stat-label">Siswa Aktif</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-green">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalGuru'] }}</div>
                <div class="stat-label">Tenaga Pendidik</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-purple">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalKelas'] }}</div>
                <div class="stat-label">Kelas Aktif</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-orange">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalCabang'] }}</div>
                <div class="stat-label">Cabang</div>
            </div>
        </div>
    </div>

    <!-- Section Title -->
    <div class="section-header">
        <i class="fas fa-print"></i>
        <h5>Pilih Jenis Laporan</h5>
    </div>

    <!-- Report Grid -->
    <div class="report-grid">

        {{-- Laporan Siswa --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <div class="rpt-card-icon bg-rpt-blue">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="rpt-card-info">
                    <h6 class="rpt-card-title">Daftar Siswa</h6>
                    <p class="rpt-card-subtitle">Cetak berdasarkan cabang, jenjang & kelas</p>
                </div>
            </div>
            <div class="rpt-card-body">
                <form action="{{ route('admin.laporan.siswa') }}" method="GET" target="_blank" class="rpt-form" id="formSiswa">
                    <div class="rpt-form-group">
                        <label>Cabang</label>
                        <select name="cabang_id" id="siswa_cabang">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rpt-form-group is-hidden" id="siswa_jenjang_group">
                        <label>Jenjang</label>
                        <select name="jenjang" id="siswa_jenjang">
                            <option value="">Semua Jenjang</option>
                        </select>
                    </div>
                    <div class="rpt-form-group is-hidden" id="siswa_kelas_group">
                        <label>Kelas</label>
                        <select name="kelas_id" id="siswa_kelas">
                            <option value="">Semua Kelas</option>
                        </select>
                    </div>
                    <div class="rpt-form-group">
                        <label>Urut Berdasarkan</label>
                        <select name="sort_by">
                            <option value="nama">Nama (Abjad)</option>
                            <option value="kelas">Per Kelas</option>
                            <option value="cabang">Per Cabang</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-rpt-print btn-rpt-blue">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Tenaga Pendidik --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <div class="rpt-card-icon bg-rpt-green">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="rpt-card-info">
                    <h6 class="rpt-card-title">Daftar Tenaga Pendidik</h6>
                    <p class="rpt-card-subtitle">Guru, wali kelas & staff lengkap</p>
                </div>
            </div>
            <div class="rpt-card-body">
                <form action="{{ route('admin.laporan.tenaga-pendidik') }}" method="GET" target="_blank" class="rpt-form">
                    <div class="rpt-form-group">
                        <label>Role</label>
                        <select name="role">
                            <option value="">Semua Role</option>
                            <option value="wali_kelas">Wali Kelas</option>
                            <option value="guru_pengajar">Guru Pengajar</option>
                            <option value="bendahara">Bendahara</option>
                            <option value="sekretaris">Sekretaris</option>
                        </select>
                    </div>
                    <div class="rpt-form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                    <div class="rpt-form-group">
                        <label>Urut Berdasarkan</label>
                        <select name="sort_by">
                            <option value="nama">Nama (Abjad)</option>
                            <option value="nip">NIP</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-rpt-print btn-rpt-green">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Kelas --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <div class="rpt-card-icon bg-rpt-purple">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="rpt-card-info">
                    <h6 class="rpt-card-title">Daftar Kelas</h6>
                    <p class="rpt-card-subtitle">Info wali kelas, siswa & kuota</p>
                </div>
            </div>
            <div class="rpt-card-body">
                <form action="{{ route('admin.laporan.kelas') }}" method="GET" target="_blank" class="rpt-form">
                    <div class="rpt-form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rpt-form-group">
                        <label>Jenjang</label>
                        <select name="jenjang">
                            <option value="">Semua Jenjang</option>
                            <option value="PAUD">PAUD</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                        </select>
                    </div>
                    <div class="rpt-form-group">
                        <label>Cabang</label>
                        <select name="cabang_id">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-rpt-print btn-rpt-purple">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Wali Kelas --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <div class="rpt-card-icon bg-rpt-orange">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="rpt-card-info">
                    <h6 class="rpt-card-title">Daftar Wali Kelas</h6>
                    <p class="rpt-card-subtitle">Kelas yang diwalikan & jumlah siswa</p>
                </div>
            </div>
            <div class="rpt-card-body">
                <form action="{{ route('admin.laporan.wali-kelas') }}" method="GET" target="_blank" class="rpt-form">
                    <div class="rpt-form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="rpt-form-group">
                        <label>Jenjang</label>
                        <select name="jenjang">
                            <option value="">Semua Jenjang</option>
                            <option value="PAUD">PAUD</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-rpt-print btn-rpt-orange">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Guru Pengajar --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <div class="rpt-card-icon bg-rpt-teal">
                    <i class="fas fa-users"></i>
                </div>
                <div class="rpt-card-info">
                    <h6 class="rpt-card-title">Daftar Guru Pengajar</h6>
                    <p class="rpt-card-subtitle">Kelas & mata pelajaran yang diajar</p>
                </div>
            </div>
            <div class="rpt-card-body">
                <form action="{{ route('admin.laporan.guru-pengajar') }}" method="GET" target="_blank" class="rpt-form">
                    <div class="rpt-form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-rpt-print btn-rpt-teal">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Rekap Statistik --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <div class="rpt-card-icon bg-rpt-pink">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="rpt-card-info">
                    <h6 class="rpt-card-title">Rekap Statistik</h6>
                    <p class="rpt-card-subtitle">Statistik per cabang & jenjang</p>
                </div>
            </div>
            <div class="rpt-card-body">
                <form action="{{ route('admin.laporan.rekap') }}" method="GET" target="_blank" class="rpt-form">
                    <div class="rpt-form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-rpt-print btn-rpt-pink">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/laporan/index.js'])
@endsection
