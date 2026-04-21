@extends('layouts.sneat')

@section('title', 'Cetak Laporan')

@section('page-title', 'Cetak Laporan')
@section('page-subtitle', 'Cetak berbagai jenis laporan data akademik')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --rpt-primary: #4361ee;
        --rpt-success: #10b981;
        --rpt-warning: #f59e0b;
        --rpt-danger: #ef4444;
        --rpt-info: #06b6d4;
        --rpt-purple: #8b5cf6;
        --rpt-teal: #14b8a6;
        --rpt-pink: #ec4899;
        --rpt-surface: #ffffff;
        --rpt-bg: #f8fafc;
        --rpt-border: #e2e8f0;
        --rpt-text: #1e293b;
        --rpt-muted: #64748b;
        --rpt-radius: 12px;
    }

    /* Stat Cards */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: var(--rpt-surface);
        border: 1px solid var(--rpt-border);
        border-radius: var(--rpt-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease;
    }

    .stat-widget:hover { transform: translateY(-2px); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-details { flex-grow: 1; }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--rpt-text);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--rpt-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Section Title */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .section-header h5 {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--rpt-text);
        margin: 0;
    }
    .section-header i {
        color: var(--rpt-muted);
        font-size: 1rem;
    }

    /* Report Grid */
    .report-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    /* Report Card */
    .rpt-card {
        background: var(--rpt-surface);
        border: 1px solid var(--rpt-border);
        border-radius: var(--rpt-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .rpt-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .rpt-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--rpt-border);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .rpt-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .rpt-card-info { flex-grow: 1; }

    .rpt-card-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--rpt-text);
        margin: 0;
    }

    .rpt-card-subtitle {
        font-size: 0.75rem;
        color: var(--rpt-muted);
        margin: 2px 0 0 0;
    }

    .rpt-card-body {
        padding: 1.25rem 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    /* Form Styles */
    .rpt-form {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        flex-grow: 1;
    }

    .rpt-form-group { display: flex; flex-direction: column; gap: 0.35rem; }

    .rpt-form-group label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--rpt-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .rpt-form-group select,
    .rpt-form-group input {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--rpt-border);
        border-radius: 8px;
        font-size: 0.85rem;
        color: var(--rpt-text);
        background: var(--rpt-surface);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .rpt-form-group select:focus,
    .rpt-form-group input:focus {
        outline: none;
        border-color: var(--rpt-primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .btn-rpt-print {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        color: white;
        margin-top: auto;
        width: 100%;
    }

    .btn-rpt-print:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        color: white;
    }

    /* Color Variants */
    .bg-rpt-blue { background: #eff6ff; color: #3b82f6; }
    .bg-rpt-green { background: #ecfdf5; color: #10b981; }
    .bg-rpt-purple { background: #f5f3ff; color: #8b5cf6; }
    .bg-rpt-orange { background: #fffbeb; color: #f59e0b; }
    .bg-rpt-teal { background: #f0fdfa; color: #14b8a6; }
    .bg-rpt-pink { background: #fdf2f8; color: #ec4899; }

    .btn-rpt-blue { background: #3b82f6; }
    .btn-rpt-blue:hover { background: #2563eb; }
    .btn-rpt-green { background: #10b981; }
    .btn-rpt-green:hover { background: #059669; }
    .btn-rpt-purple { background: #8b5cf6; }
    .btn-rpt-purple:hover { background: #7c3aed; }
    .btn-rpt-orange { background: #f59e0b; }
    .btn-rpt-orange:hover { background: #d97706; }
    .btn-rpt-teal { background: #14b8a6; }
    .btn-rpt-teal:hover { background: #0d9488; }
    .btn-rpt-pink { background: #ec4899; }
    .btn-rpt-pink:hover { background: #db2777; }

    /* Responsive */
    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .report-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); }
        .report-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 576px) {
        .stat-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalSiswa'] }}</div>
                <div class="stat-label">Siswa Aktif</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalGuru'] }}</div>
                <div class="stat-label">Tenaga Pendidik</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalKelas'] }}</div>
                <div class="stat-label">Kelas Aktif</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
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
                    <div class="rpt-form-group" id="siswa_jenjang_group" style="display:none;">
                        <label>Jenjang</label>
                        <select name="jenjang" id="siswa_jenjang">
                            <option value="">Semua Jenjang</option>
                        </select>
                    </div>
                    <div class="rpt-form-group" id="siswa_kelas_group" style="display:none;">
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

@push('scripts')
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
@endpush
@endsection
