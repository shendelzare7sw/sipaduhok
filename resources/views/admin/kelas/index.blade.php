@extends('layouts.sneat')

@section('title', 'Data Kelas')

@section('page-title', 'Data Kelas')
@section('page-subtitle')
Kelola data kelas {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Styling variables inherited from Sneat layout or custom */
    :root {
        --kls-primary: #4361ee;
        --kls-success: #10b981;
        --kls-warning: #f59e0b;
        --kls-danger: #ef4444;
        --kls-info: #06b6d4;
        --kls-purple: #8b5cf6;
        --kls-surface: #ffffff;
        --kls-bg: #f8fafc;
        --kls-border: #e2e8f0;
        --kls-text: #1e293b;
        --kls-muted: #64748b;
        --kls-radius: 12px;
    }

    .kls-card {
        background: var(--kls-surface);
        border: 1px solid var(--kls-border);
        border-radius: var(--kls-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .kls-card-header {
        background: transparent;
        border-bottom: 1px solid var(--kls-border);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .kls-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--kls-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
        background: var(--kls-surface);
        border: 1px solid var(--kls-border);
        border-radius: var(--kls-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease;
    }

    .stat-widget:hover {
        transform: translateY(-2px);
    }

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

    .stat-details {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--kls-text);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--kls-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-desc {
        font-size: 0.7rem;
        color: var(--kls-muted);
        margin-top: 0.2rem;
    }

    /* Table Improvements */
    .table-clean {
        margin: 0;
    }
    
    .table-clean th {
        background: var(--kls-bg);
        border-bottom: 1px solid var(--kls-border);
        color: var(--kls-muted);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
    }

    .table-clean td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--kls-border);
        color: var(--kls-text);
        font-size: 0.9rem;
    }

    .table-clean tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-clean tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-jnj {
        padding: 0.35em 0.6em;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
    }

    /* Jenjang badges */
    .bg-jnj-kb { background: #fef3c7; color: #92400e; }
    .bg-jnj-tka { background: #fed7aa; color: #9a3412; }
    .bg-jnj-tkb { background: #fecaca; color: #991b1b; }
    .bg-jnj-sd { background: #dcfce7; color: #166534; }
    .bg-jnj-smp { background: #e0f2fe; color: #075985; }
    .bg-jnj-sma { background: #f3e8ff; color: #7c3aed; }

    .action-btns .btn {
        padding: 0.35rem 0.6rem;
        font-size: 0.8rem;
        border-radius: 6px;
    }

    /* Filters */
    .filter-wrapper {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
        background: var(--kls-bg);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--kls-border);
    }
    
    .search-box {
        position: relative;
        flex-grow: 1;
        min-width: 200px;
    }
    .search-box input {
        width: 100%;
        padding: 0.45rem 1rem 0.45rem 2.2rem;
        border: 1px solid var(--kls-border);
        border-radius: 8px;
        font-size: 0.85rem;
    }
    .search-box i {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--kls-muted);
    }

    .filter-select {
        min-width: 150px;
        font-size: 0.85rem;
        padding: 0.45rem 2rem 0.45rem 0.75rem;
        border-color: var(--kls-border);
        border-radius: 8px;
    }

    /* Kelas styles */
    .kelas-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .kelas-nama {
        font-weight: 600;
        color: var(--kls-text);
        font-size: 0.95rem;
    }
    .kelas-kode {
        font-size: 0.75rem;
        color: var(--kls-muted);
        background: var(--kls-bg);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
        font-family: inherit;
    }

    /* Wali Kelas */
    .wali-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .wali-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(139, 92, 246, 0.15);
        color: var(--kls-purple);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Kuota progress */
    .kuota-progress {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 120px;
    }
    .kuota-bar {
        height: 6px;
        background: var(--kls-border);
        border-radius: 4px;
        overflow: hidden;
    }
    .kuota-fill {
        height: 100%;
        border-radius: 4px;
    }
    .kuota-text {
        font-size: 0.75rem;
        color: var(--kls-text);
        text-align: right;
        font-weight: 600;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .kls-card-header { flex-direction: column; align-items: stretch; }
        .filter-wrapper { flex-direction: column; align-items: stretch; }
        
        /* Actions in header mobile */
        .header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .header-actions .btn { flex-grow: 1; justify-content: center; font-size: 0.8rem;}

        /* Mobile Card Table */
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: flex;
            flex-direction: column;
            border-bottom: 2px solid var(--kls-border);
            padding: 0;
            background: #fff;
        }
        .table-clean tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            gap: 1rem;
        }
        .table-clean tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--kls-muted);
            text-transform: uppercase;
        }
        .table-clean tbody td.mobile-card-head {
            background: var(--kls-bg);
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem;
        }
        .table-clean tbody td.mobile-card-head::before { display: none; }
        .td-actions {
            margin-top: 0;
            padding: 1rem !important;
            justify-content: space-between !important;
            background: #f8fafc;
            gap: 1rem;
        }
        .kuota-progress { width: 100%; align-items: flex-end; }
        .wali-wrapper { justify-content: flex-end; }
    }
</style>
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <!-- Total Kelas -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalKelas'] }}</div>
                <div class="stat-label">Total Kelas</div>
                <div class="stat-desc">{{ $currentTahunAjaran ? $currentTahunAjaran->nama_tahun_ajaran : 'Semua Tahun' }}</div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalSiswa'] }}</div>
                <div class="stat-label">Siswa Terdaftar</div>
                <div class="stat-desc">Di tahun akademik pilihan</div>
            </div>
        </div>

        <!-- Ada Wali -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['kelasWithWali'] }}</div>
                <div class="stat-label">Terisi Wali Kelas</div>
                <div class="stat-desc">Telah di-assign wali</div>
            </div>
        </div>

        <!-- Belum Ada Wali -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['kelasWithoutWali'] }}</div>
                <div class="stat-label">Belum Ada Wali</div>
                <div class="stat-desc text-warning">Perlu tindakan</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="kls-card">
        <div class="kls-card-header">
            <h5 class="kls-card-title">
                <i class="fas fa-list text-primary"></i> Daftar Kelas
            </h5>
            <div class="header-actions d-flex gap-2">
                <button type="button" class="btn btn-info text-white d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#copyClassModal">
                    <i class="fas fa-copy"></i> <span class="d-none d-sm-inline">Salin Data</span>
                </button>
                <a href="{{ route('admin.kelas.print', request()->query()) }}" class="btn btn-secondary text-white d-flex align-items-center gap-1" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
                <a href="{{ route('admin.kelas.import') }}" class="btn btn-success text-white d-flex align-items-center gap-1">
                    <i class="fas fa-file-import"></i> <span class="d-none d-sm-inline">Import Excel</span>
                </a>
                <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Kelas Baru</span>
                </a>
            </div>
        </div>

        <!-- Filters Form -->
        <form action="{{ route('admin.kelas.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama atau kode kelas..." value="{{ request('search') }}">
                </div>
                
                <select name="tahun_ajaran_id" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                
                <select name="jenjang" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $j)
                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                
                <select name="cabang_id" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                
                @if(request()->hasAny(['search', 'jenjang', 'cabang_id']) || (request('tahun_ajaran_id') && request('tahun_ajaran_id') != $currentTahunAjaran?->id))
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 8px;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th width="280">Info Kelas</th>
                        <th>Jenjang</th>
                        <th>Wali Kelas</th>
                        <th width="120">Kuota / Siswa</th>
                        <th class="text-end" width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $k)
                    <tr>
                        <td class="mobile-card-head" data-label="Kelas">
                            <div class="kelas-info">
                                <span class="kelas-nama">{{ $k->nama_kelas }}</span>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="kelas-kode">{{ $k->kode_kelas }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-building me-1"></i>{{ $k->cabang->nama_cabang ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Jenjang">
                            @php
                                $jenjangClass = [
                                    'KB' => 'bg-jnj-kb', 'TKA' => 'bg-jnj-tka', 'TKB' => 'bg-jnj-tkb',
                                    'SD' => 'bg-jnj-sd', 'SMP' => 'bg-jnj-smp', 'SMA' => 'bg-jnj-sma',
                                ][$k->jenjang] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $jenjangClass }} badge-jnj">{{ $k->jenjang }}</span>
                        </td>
                        <td data-label="Wali Kelas">
                            @if($k->waliKelasAssignments->count() > 0)
                                <div class="d-flex flex-column gap-1">
                                @foreach($k->waliKelasAssignments as $assignment)
                                    <div class="wali-wrapper">
                                        <div class="wali-avatar">{{ strtoupper(substr($assignment->tenagaPendidik->nama_lengkap, 0, 1)) }}</div>
                                        <span style="font-size: 0.85rem;" class="fw-medium text-dark">{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                    </div>
                                @endforeach
                                </div>
                            @else
                                <span class="badge bg-label-warning px-2 py-1"><i class="fas fa-exclamation-circle me-1"></i> Belum ada</span>
                            @endif
                        </td>
                        <td data-label="Kuota Siswa">
                            @php
                                $percentage = $k->kuota_siswa > 0 ? ($k->siswa_count / $k->kuota_siswa) * 100 : 0;
                                $barColor = $percentage < 50 ? '#10b981' : ($percentage < 80 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <div class="kuota-progress">
                                <span class="kuota-text">
                                    <span style="color: {{ $barColor }}">{{ $k->siswa_count }}</span> / {{ $k->kuota_siswa ?: '-' }}
                                </span>
                                <div class="kuota-bar">
                                    <div class="kuota-fill" style="width: {{ min($percentage, 100) }}%; background: {{ $barColor }};"></div>
                                </div>
                            </div>
                        </td>
                        <td class="td-actions text-end" data-label="Aksi">
                            <div class="d-flex justify-content-end gap-1 action-btns">
                                <a href="{{ route('admin.kelas.show', $k->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.kelas.edit', $k->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger text-white" onclick="confirmDelete({{ $k->id }}, '{{ addslashes($k->nama_kelas) }}')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-chalkboard fs-1 mb-3" style="color: #e2e8f0;"></i>
                                <h6 class="mb-1">Tidak Ada Data Kelas</h6>
                                <p class="small mb-0">Belum ada kelas yang ditambahkan atau tidak ada hasil pencarian.</p>
                                <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary btn-sm mt-3 px-3 rounded-pill">
                                    <i class="fas fa-plus me-1"></i> Tambah Kelas Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kelas->hasPages())
        <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-muted small">Menampilkan {{ $kelas->firstItem() ?? 0 }} - {{ $kelas->lastItem() ?? 0 }} dari total {{ $kelas->total() }}</span>
            <div class="mt-2 mt-sm-0">
                {{ $kelas->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-trash-alt fs-3 text-danger"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Kelas?</h5>
                <p class="text-muted mb-4" style="font-size: 0.9rem;">Kelas <span id="deleteItemName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Class Modal -->
<div class="modal fade" id="copyClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary px-4 py-3">
                <h5 class="modal-title text-white m-0 d-flex align-items-center gap-2"><i class="fas fa-copy"></i> Salin Data Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelas.copy') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="alert alert-info py-2 px-3 mb-4 rounded border-0" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-1"></i> Menyalin kelas hanya akan menyalin data master kelas, tidak termasuk data siswa dan guru pengajar.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Dari Tahun Ajaran</label>
                        <select name="from_tahun_ajaran_id" class="form-select form-select-lg" required>
                            <option value="">Pilih Tahun Ajaran Asal...</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Ke Tahun Ajaran</label>
                        <select name="to_tahun_ajaran_id" class="form-select form-select-lg" required>
                            <option value="">Pilih Tahun Ajaran Tujuan...</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $currentTahunAjaran && $currentTahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 bg-light border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-paste me-2"></i>Mulai Menyalin</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteItemName').textContent = name;
        const form = document.getElementById('deleteForm');
        form.action = "{{ route('admin.kelas.index') }}/" + id;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endsection
