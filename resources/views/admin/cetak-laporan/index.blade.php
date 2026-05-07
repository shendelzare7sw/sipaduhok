@extends('layouts.sneat')

@section('title', 'Cetak Laporan')

@section('page-title', 'Cetak Laporan')
@section('page-subtitle', 'Cetak berbagai jenis laporan')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .stats-row { grid-template-columns: 1fr; } }

.stat-mini {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.stat-mini-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-mini-icon.blue { background: #eff6ff; }
.stat-mini-icon.green { background: #f0fdf4; }
.stat-mini-icon.purple { background: #faf5ff; }
.stat-mini-icon.orange { background: #fff7ed; }

.stat-mini-info h4 { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }
.stat-mini-info p { font-size: 13px; color: #6b7280; margin: 0; }

.report-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

@media (max-width: 992px) { .report-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .report-grid { grid-template-columns: 1fr; } }

.report-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.report-card-header {
    padding: 24px;
    color: white;
    position: relative;
    overflow: hidden;
}

.report-card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -30%;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.report-card-header.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.report-card-header.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.report-card-header.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.report-card-header.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.report-card-header.teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
.report-card-header.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

.report-card-icon { font-size: 32px; margin-bottom: 12px; position: relative; z-index: 1; }
.report-card-title { font-size: 18px; font-weight: 600; margin: 0; position: relative; z-index: 1; }

.report-card-body { padding: 24px; }
.report-card-desc { font-size: 14px; color: #6b7280; margin-bottom: 20px; line-height: 1.6; }

.report-form { display: flex; flex-direction: column; gap: 12px; }

.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 12px; font-weight: 600; color: #374151; text-transform: uppercase; }

.form-group select, .form-group input {
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-group select:focus, .form-group input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-print {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    color: white;
    margin-top: 8px;
}

.btn-print.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.btn-print.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.btn-print.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.btn-print.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.btn-print.teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
.btn-print.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

.btn-print:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i { color: #6b7280; }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
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
                    <div class="form-group" id="siswa_jenjang_group" style="display:none;">
                        <label>Jenjang</label>
                        <select name="jenjang" id="siswa_jenjang">
                            <option value="">Semua Jenjang</option>
                        </select>
                    </div>
                    <div class="form-group" id="siswa_kelas_group" style="display:none;">
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
            <div class="report-card-header" style="background: linear-gradient(135deg, #16a34a, #15803d); color: white;">
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
                    <button type="submit" class="btn-print" style="background: #16a34a;">
                        <i class="fas fa-print"></i> Cetak Rekap Akademik
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @php
        $kelasJson = $kelasList->map(function($k) {
            return ['id' => $k->id, 'nama_kelas' => $k->nama_kelas, 'jenjang' => $k->jenjang, 'cabang_id' => $k->cabang_id];
        })->values();
    @endphp
    const kelasData = @json($kelasJson);

    const cabangSelect = document.getElementById('siswa_cabang');
    const jenjangGroup = document.getElementById('siswa_jenjang_group');
    const jenjangSelect = document.getElementById('siswa_jenjang');
    const kelasGroup = document.getElementById('siswa_kelas_group');
    const kelasSelect = document.getElementById('siswa_kelas');

    cabangSelect.addEventListener('change', function() {
        const cabangId = this.value;
        jenjangSelect.innerHTML = '<option value="">Semua Jenjang</option>';
        kelasSelect.innerHTML = '<option value="">Semua Kelas</option>';

        if (cabangId) {
            const jenjangs = [...new Set(kelasData.filter(k => k.cabang_id == cabangId).map(k => k.jenjang))];
            jenjangs.sort();
            jenjangs.forEach(j => {
                jenjangSelect.innerHTML += `<option value="${j}">${j}</option>`;
            });
            jenjangGroup.style.display = 'flex';
        } else {
            jenjangGroup.style.display = 'none';
            kelasGroup.style.display = 'none';
        }
    });

    jenjangSelect.addEventListener('change', function() {
        const cabangId = cabangSelect.value;
        const jenjang = this.value;
        kelasSelect.innerHTML = '<option value="">Semua Kelas</option>';

        if (jenjang) {
            const filtered = kelasData.filter(k => k.cabang_id == cabangId && k.jenjang == jenjang);
            filtered.forEach(k => {
                kelasSelect.innerHTML += `<option value="${k.id}">${k.nama_kelas}</option>`;
            });
            kelasGroup.style.display = 'flex';
        } else {
            kelasGroup.style.display = 'none';
        }
    });
});
</script>
@endsection
