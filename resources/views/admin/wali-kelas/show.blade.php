@extends('layouts.sneat')

@section('title', 'Detail Wali Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Detail Wali Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    font-size: 14px;
}

.breadcrumb a {
    color: #6b7280;
    text-decoration: none;
}

.breadcrumb a:hover {
    color: #8b5cf6;
}

.breadcrumb span {
    color: #9ca3af;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

.header-card {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: 20px;
    padding: 32px;
    color: white;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}

.header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.header-content {
    position: relative;
    z-index: 2;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
}

.header-text h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
}

.header-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(255,255,255,0.2);
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
}

.header-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.header-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

@media (max-width: 768px) {
    .header-stats {
        grid-template-columns: 1fr;
    }
}

.header-stat {
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    padding: 16px 20px;
    backdrop-filter: blur(10px);
}

.header-stat-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
}

.header-stat-label {
    font-size: 13px;
    opacity: 0.9;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-white {
    background: white;
    color: #7c3aed;
}

.btn-white:hover {
    background: #f5f3ff;
}

.btn-white-outline {
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.5);
}

.btn-white-outline:hover {
    background: rgba(255,255,255,0.1);
    border-color: white;
}

.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    border: none;
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h5 i {
    color: #8b5cf6;
}

.card-body {
    padding: 24px;
}

.grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

@media (max-width: 992px) {
    .grid-2 {
        grid-template-columns: 1fr;
    }
}

.wali-kelas-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    border-radius: 16px;
    border: 2px solid #ddd6fe;
}

.wali-avatar {
    width: 80px;
    height: 80px;
    min-width: 80px;
    min-height: 80px;
    flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 28px;
}

.wali-info {
    flex: 1;
    min-width: 0;
}

.wali-info h3 {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.wali-info p {
    font-size: 14px;
    color: #6b7280;
    margin: 4px 0;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.wali-info p span {
    overflow-wrap: anywhere;
    word-break: break-word;
}

.wali-info p i {
    color: #8b5cf6;
    width: 16px;
    flex-shrink: 0;
    margin-top: 3px;
    text-align: center;
}

.empty-wali {
    text-align: center;
    padding: 40px;
    background: #fef3c7;
    border-radius: 16px;
    border: 2px dashed #fcd34d;
}

.empty-wali i {
    font-size: 48px;
    color: #f59e0b;
    margin-bottom: 12px;
}

.empty-wali h4 {
    color: #92400e;
    margin-bottom: 8px;
}

.empty-wali p {
    color: #b45309;
    font-size: 14px;
}

.info-grid {
    display: grid;
    gap: 16px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    font-weight: 600;
}

.info-value {
    font-size: 15px;
    color: #111827;
}

.assign-form {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.assign-form label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.assign-form select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 16px;
}

.assign-form select:focus {
    outline: none;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 12px 16px;
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    font-size: 14px;
}

.table tr:hover td {
    background: #f9fafb;
}

.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
}

.badge-info { background: #e0f2fe; color: #075985; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

@media (max-width: 767.98px) {
    .header-card { padding: 20px; }
    .header-icon { width: 56px; height: 56px; font-size: 24px; border-radius: 12px; }
    .header-text h1 { font-size: 20px; }
    .header-info { flex-wrap: wrap; }
    .header-actions { width: 100%; }
    .header-actions .btn { flex: 1; justify-content: center; }

    .wali-kelas-card {
        flex-direction: column;
        text-align: center;
        padding: 20px;
        gap: 16px;
    }
    .wali-info {
        width: 100%;
    }
    .wali-info h3 {
        font-size: 18px;
    }
    .wali-info p {
        justify-content: center;
    }

    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        background: white;
    }
    .table-card-mobile tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 4px;
        border: none;
        font-size: 13px;
    }
    .table-card-mobile tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6b7280;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        margin-right: 8px;
    }
    .table-card-mobile tbody td.mobile-card-hide { display: none; }
    .table-card-mobile tbody td.mobile-card-head {
        display: flex;
        align-items: center;
        padding-bottom: 8px;
        margin-bottom: 4px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        font-weight: 600;
    }
    .table-card-mobile tbody td.mobile-card-head::before { display: none; }
}
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.wali-kelas.index') }}">Data Wali Kelas</a>
        <span>/</span>
        <span class="current">{{ $kelas->nama_kelas }}</span>
    </div>

    <div class="header-card">
        <div class="header-content">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-icon"><i class="fas fa-chalkboard"></i></div>
                    <div class="header-text">
                        <h1>Kelas {{ $kelas->nama_kelas }}</h1>
                        <div class="header-meta">
                            <span class="header-badge">
                                <i class="fas fa-tag"></i> {{ $kelas->kode_kelas }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-layer-group"></i> {{ $kelas->jenjang }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('admin.kelas.show', $kelas) }}" class="btn btn-white">
                        <i class="fas fa-eye"></i> Detail Kelas
                    </a>
                    <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-white-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['totalSiswa'] }}</div>
                    <div class="header-stat-label">Total Siswa</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['siswaLaki'] }}</div>
                    <div class="header-stat-label">Laki-laki</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $stats['siswaPerempuan'] }}</div>
                    <div class="header-stat-label">Perempuan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        {{-- Wali Kelas Info --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-tie"></i> Wali Kelas</h5>
            </div>
            <div class="card-body">
                @if($kelas->waliKelas)
                    <div class="wali-kelas-card">
                        <div class="wali-avatar">{{ strtoupper(substr($kelas->waliKelas->nama_lengkap, 0, 1)) }}</div>
                        <div class="wali-info">
                            <h3>{{ $kelas->waliKelas->nama_lengkap }}</h3>
                            <p><i class="fas fa-id-badge"></i> <span>NIP: {{ $kelas->waliKelas->nip ?? '-' }}</span></p>
                            <p><i class="fas fa-phone"></i> <span>{{ $kelas->waliKelas->telepon ?? '-' }}</span></p>
                            <p><i class="fas fa-envelope"></i> <span>{{ $kelas->waliKelas->user->email ?? '-' }}</span></p>
                        </div>
                    </div>
                @else
                    <div class="empty-wali">
                        <i class="fas fa-user-slash"></i>
                        <h4>Belum Ada Wali Kelas</h4>
                        <p>Silakan tunjuk wali kelas untuk kelas ini</p>
                    </div>
                @endif

                <div class="assign-form">
                    <button type="button" id="toggleAssignBtn" onclick="toggleAssignForm()" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-user-edit"></i> {{ $kelas->waliKelas ? 'Ganti Wali Kelas' : 'Tunjuk Wali Kelas' }}
                    </button>

                    <div id="assignFormPanel" style="display: none; margin-top: 16px;">
                        <form action="{{ route('admin.wali-kelas.assign', $kelas) }}" method="POST" id="assignForm">
                            @csrf

                            <div style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                                <div style="position: relative; flex: 1; min-width: 150px;">
                                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                                    <input type="text" id="searchWali" placeholder="Cari nama guru..." style="width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
                                </div>
                                <select id="filterCabang" style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; min-width: 140px;">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="waliList" style="max-height: 250px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 10px; padding: 6px;">
                                @foreach($waliKelasOptions as $wk)
                                    @php $assignedKelas = $wk->waliKelasAssignments ?? collect(); @endphp
                                    <label class="wali-option"
                                           data-name="{{ strtolower($wk->nama_lengkap) }}"
                                           data-cabang="{{ $wk->user->cabang_id ?? '' }}"
                                           style="display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; cursor: pointer; margin-bottom: 2px; transition: background .15s;">
                                        <input type="radio" name="wali_kelas_id" value="{{ $wk->id }}" style="cursor: pointer; flex-shrink: 0;"
                                            {{ $kelas->wali_kelas_id == $wk->id ? 'checked' : '' }}>
                                        <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; flex-shrink: 0;">
                                            {{ strtoupper(substr($wk->nama_lengkap, 0, 2)) }}
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 600; font-size: 13px; color: #111827;">{{ $wk->nama_lengkap }}</div>
                                            <div style="font-size: 11px; color: #6b7280;">{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                            @if($assignedKelas->count() > 0)
                                                <div style="font-size: 10px; color: #6366f1; margin-top: 2px;">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                    {{ $assignedKelas->map(fn($a) => $a->kelas->nama_kelas ?? '')->filter()->join(', ') }}
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div style="display: flex; gap: 8px; margin-top: 16px;">
                                <button type="button" onclick="toggleAssignForm()" class="btn" style="flex: 1; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                                <button type="submit" class="btn btn-primary" style="flex: 1;">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kelas Info --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Kelas</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Kode Kelas</span>
                        <span class="info-value" style="font-family: monospace; color: #8b5cf6; font-weight: 600;">{{ $kelas->kode_kelas }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Kelas</span>
                        <span class="info-value">{{ $kelas->nama_kelas }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenjang</span>
                        <span class="info-value">{{ $kelas->jenjang }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Cabang</span>
                        <span class="info-value">{{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tahun Ajaran</span>
                        <span class="info-value">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kuota Siswa</span>
                        <span class="info-value">{{ $kelas->kuota_siswa }} siswa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-user-graduate"></i> Daftar Siswa ({{ $stats['totalSiswa'] }})</h5>
        </div>
        <div class="card-body">
            @if($kelas->siswa->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th class="mobile-card-hide">No</th>
                                <th data-label="NIS">NIS</th>
                                <th class="mobile-card-head">Nama Siswa</th>
                                <th data-label="Jenis Kelamin">Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->siswa->sortBy('nama_lengkap') as $index => $siswa)
                            <tr>
                                <td class="mobile-card-hide">{{ $index + 1 }}</td>
                                <td data-label="NIS"><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $siswa->nis }}</code></td>
                                <td class="mobile-card-head">
                                    <div class="user-info">
                                        <div class="user-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
                                        <span>{{ $siswa->nama_lengkap }}</span>
                                    </div>
                                </td>
                                <td data-label="Jenis Kelamin">
                                    <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Belum ada siswa di kelas ini</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleAssignForm() {
    const panel = document.getElementById('assignFormPanel');
    const btn = document.getElementById('toggleAssignBtn');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        btn.style.display = 'none';
    } else {
        panel.style.display = 'none';
        btn.style.display = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchWali');
    const filterCabang = document.getElementById('filterCabang');
    if (!searchInput) return;

    function filterWali() {
        const term = searchInput.value.toLowerCase();
        const cabang = filterCabang.value;
        document.querySelectorAll('.wali-option').forEach(el => {
            const name = el.getAttribute('data-name');
            const cb = el.getAttribute('data-cabang');
            const matchName = !term || name.includes(term);
            const matchCabang = !cabang || cb === cabang;
            el.style.display = (matchName && matchCabang) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterWali);
    filterCabang.addEventListener('change', filterWali);

    // Highlight selected option
    document.querySelectorAll('.wali-option').forEach(label => {
        label.addEventListener('click', function() {
            document.querySelectorAll('.wali-option').forEach(l => l.style.background = '');
            this.style.background = '#f5f3ff';
        });
        if (label.querySelector('input:checked')) label.style.background = '#f5f3ff';
    });
});
</script>
@endsection
