@extends('layouts.sneat')

@section('title', 'Cetak Laporan')

@section('page-title', 'Cetak Laporan')
@section('page-subtitle', 'Cetak berbagai jenis laporan')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/cetak-laporan/index.css'])
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

@section('content')
<div class="admin-cetak-laporan-page">
    <template id="cetakLaporanKelasData">@json($kelasJson)</template>
    {{-- Quick Stats --}}
    <div class="stats-row">
        <div class="stat-mini">
            <div class="stat-mini-icon blue"><i class="fas fa-graduation-cap"></i></div>
            <div class="stat-mini-info">
                <h4>{{ $stats['totalSiswa'] }}</h4>
                <p>Siswa Aktif</p>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon green"><i class="fas fa-school"></i></div>
            <div class="stat-mini-info">
                <h4>{{ $stats['totalGuru'] }}</h4>
                <p>Tenaga Pendidik</p>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon purple"><i class="fas fa-books"></i></div>
            <div class="stat-mini-info">
                <h4>{{ $stats['totalKelas'] }}</h4>
                <p>Kelas Aktif</p>
            </div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-icon orange"><i class="fas fa-school"></i></div>
            <div class="stat-mini-info">
                <h4>{{ $stats['totalCabang'] }}</h4>
                <p>Cabang</p>
            </div>
        </div>
    </div>

    <h3 class="section-title"><i class="fas fa-print"></i> Pilih Jenis Laporan</h3>

    <div class="report-grid">
        {{-- Laporan Siswa --}}
        <div class="report-card">
            <div class="report-card-header blue">
                <div class="report-card-icon"><i class="fas fa-graduation-cap"></i></div>
                <h4 class="report-card-title">Daftar Siswa</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Cetak daftar siswa berdasarkan cabang, jenjang, kelas. Dapat diurutkan per abjad, kelas, atau lokasi.</p>
                <form action="{{ route('admin.cetak-laporan.siswa') }}" method="GET" target="_blank" class="report-form" id="formSiswa">
                    <div class="form-group">
                        <label>Cabang</label>
                        <select name="cabang_id" id="siswa_cabang">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group is-hidden" id="siswa_jenjang_group">
                        <label>Jenjang</label>
                        <select name="jenjang" id="siswa_jenjang">
                            <option value="">Semua Jenjang</option>
                        </select>
                    </div>
                    <div class="form-group is-hidden" id="siswa_kelas_group">
                        <label>Kelas</label>
                        <select name="kelas_id" id="siswa_kelas">
                            <option value="">Semua Kelas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Siswa</label>
                        <select name="status">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus / Alumni</option>
                            <option value="">Semua Status</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urut Berdasarkan</label>
                        <select name="sort_by">
                            <option value="nama">Nama (Abjad)</option>
                            <option value="kelas">Per Kelas</option>
                            <option value="cabang">Per Cabang</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-print blue">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Tenaga Pendidik --}}
        <div class="report-card">
            <div class="report-card-header green">
                <div class="report-card-icon"><i class="fas fa-school"></i></div>
                <h4 class="report-card-title">Daftar Tenaga Pendidik</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Cetak daftar tenaga pendidik (guru, wali kelas, staff) dengan data lengkap.</p>
                <form action="{{ route('admin.cetak-laporan.tenaga-pendidik') }}" method="GET" target="_blank" class="report-form">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role">
                            <option value="">Semua Role</option>
                            <option value="wali_kelas">Wali Kelas</option>
                            <option value="guru_pengajar">Guru Pengajar</option>
                            <option value="bendahara">Bendahara</option>
                            <option value="sekretaris">Sekretaris</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urut Berdasarkan</label>
                        <select name="sort_by">
                            <option value="nama">Nama (Abjad)</option>
                            <option value="nip">NIP</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-print green">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Kelas --}}
        <div class="report-card">
            <div class="report-card-header purple">
                <div class="report-card-icon"><i class="fas fa-books"></i></div>
                <h4 class="report-card-title">Daftar Kelas</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Cetak daftar kelas dengan informasi wali kelas, jumlah siswa, dan kuota.</p>
                <form action="{{ route('admin.cetak-laporan.kelas') }}" method="GET" target="_blank" class="report-form">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenjang</label>
                        <select name="jenjang">
                            <option value="">Semua Jenjang</option>
                            <option value="PAUD">PAUD</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cabang</label>
                        <select name="cabang_id">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-print purple">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Wali Kelas --}}
        <div class="report-card">
            <div class="report-card-header orange">
                <div class="report-card-icon"><i class="fas fa-user-tie"></i></div>
                <h4 class="report-card-title">Daftar Wali Kelas</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Cetak daftar wali kelas beserta kelas yang diwalikan dan jumlah siswa.</p>
                <form action="{{ route('admin.cetak-laporan.wali-kelas') }}" method="GET" target="_blank" class="report-form">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenjang</label>
                        <select name="jenjang">
                            <option value="">Semua Jenjang</option>
                            <option value="PAUD">PAUD</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-print orange">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Laporan Guru Pengajar --}}
        <div class="report-card">
            <div class="report-card-header teal">
                <div class="report-card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h4 class="report-card-title">Daftar Guru Pengajar</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Cetak daftar guru pengajar beserta kelas dan mata pelajaran yang diajar.</p>
                <form action="{{ route('admin.cetak-laporan.guru-pengajar') }}" method="GET" target="_blank" class="report-form">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-print teal">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Rekap Statistik --}}
        <div class="report-card">
            <div class="report-card-header pink">
                <div class="report-card-icon"><i class="fas fa-chart-bar"></i></div>
                <h4 class="report-card-title">Rekap Statistik</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Cetak rekap statistik sekolah per cabang dan per jenjang pendidikan.</p>
                <form action="{{ route('admin.cetak-laporan.rekap') }}" method="GET" target="_blank" class="report-form">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-print pink">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </form>
            </div>
        </div>

        {{-- Rekap Akademik per TA (snapshot status_naik_kelas_siswa) --}}
        <div class="report-card">
            <div class="report-card-header academic">
                <div class="report-card-icon"><i class="fas fa-user-graduate"></i></div>
                <h4 class="report-card-title">Rekap Akademik per TA</h4>
            </div>
            <div class="report-card-body">
                <p class="report-card-desc">Snapshot kenaikan kelas / kelulusan / dispensasi per tahun ajaran. Data dari hasil promosi.</p>
                <form action="{{ route('admin.cetak-laporan.rekap-akademik') }}" method="GET" target="_blank" class="report-form">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $tahunAjaranAktif && $ta->id == $tahunAjaranAktif->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cabang</label>
                        <select name="cabang_id">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-print academic">
                        <i class="fas fa-print"></i> Cetak Rekap Akademik
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/admin/cetak-laporan/index.js'])
@endsection
