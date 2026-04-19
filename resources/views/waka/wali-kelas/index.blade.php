@extends('layouts.sneat')

@section('title', 'Data Wali Kelas')

@section('page-title', 'Data Wali Kelas')
@section('page-subtitle')
Kelola penunjukan wali kelas {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Styling variables inherited from Sneat layout or custom */
    :root {
        --wk-primary: #4361ee;
        --wk-success: #10b981;
        --wk-warning: #f59e0b;
        --wk-danger: #ef4444;
        --wk-info: #06b6d4;
        --wk-purple: #8b5cf6;
        --wk-surface: #ffffff;
        --wk-bg: #f8fafc;
        --wk-border: #e2e8f0;
        --wk-text: #1e293b;
        --wk-muted: #64748b;
        --wk-radius: 12px;
    }

    .wk-card {
        background: var(--wk-surface);
        border: 1px solid var(--wk-border);
        border-radius: var(--wk-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .wk-card-header {
        background: transparent;
        border-bottom: 1px solid var(--wk-border);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .wk-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--wk-text);
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
        background: var(--wk-surface);
        border: 1px solid var(--wk-border);
        border-radius: var(--wk-radius);
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
        color: var(--wk-text);
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--wk-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-desc {
        font-size: 0.7rem;
        color: var(--wk-muted);
        margin-top: 0.2rem;
    }

    /* Table Improvements */
    .table-clean {
        margin: 0;
    }
    
    .table-clean th {
        background: var(--wk-bg);
        border-bottom: 1px solid var(--wk-border);
        color: var(--wk-muted);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
    }

    .table-clean td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--wk-border);
        color: var(--wk-text);
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
    .bg-jnj-paud { background: #fef3c7; color: #92400e; }
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
        background: var(--wk-bg);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--wk-border);
    }
    
    .search-box {
        position: relative;
        flex-grow: 1;
        min-width: 200px;
    }
    .search-box input {
        width: 100%;
        padding: 0.45rem 1rem 0.45rem 2.2rem;
        border: 1px solid var(--wk-border);
        border-radius: 8px;
        font-size: 0.85rem;
    }
    .search-box i {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--wk-muted);
    }

    .filter-select {
        min-width: 130px;
        max-width: 160px;
        font-size: 0.85rem;
        padding: 0.45rem 2rem 0.45rem 0.75rem;
        border-color: var(--wk-border);
        border-radius: 8px;
    }

    /* Kelas & Wali styles */
    .kelas-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .kelas-nama {
        font-weight: 600;
        color: var(--wk-text);
        font-size: 0.95rem;
    }
    .kelas-kode {
        font-size: 0.75rem;
        color: var(--wk-muted);
        background: var(--wk-bg);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
        font-family: inherit;
    }

    .wali-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.35rem;
    }
    .wali-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(139, 92, 246, 0.15);
        color: var(--wk-purple);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .status-unassigned {
        color: #f59e0b;
        font-size: 0.85rem;
        font-style: italic;
    }

    /* Modal override */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .wali-option input[type="radio"]:checked {
        accent-color: #8b5cf6;
    }
    .wali-option {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .wali-option:nth-child(even) {
        background: #f8fafc;
    }
    .wali-option:hover {
        background: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
    }
    .wali-option:has(input:checked) {
        background: #eff6ff !important;
        border-color: #3b82f6 !important;
    }
    #waliList::-webkit-scrollbar { width: 6px; }
    #waliList::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    #waliList::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    
    @media (max-width: 576px) {
        .modal-footer {
            flex-direction: column-reverse;
            gap: 0.75rem !important;
            padding: 1.5rem !important;
        }
        .modal-footer > .btn, 
        .modal-footer > .d-flex,
        .modal-footer > .d-flex > .btn {
            width: 100% !important;
            margin: 0 !important;
        }
        .modal-footer > .d-flex {
            flex-direction: column-reverse;
            gap: 0.75rem !important;
        }
        .btn-responsive {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 42px !important;
            padding: 0.5rem 1.25rem !important;
            white-space: nowrap !important;
            font-size: 0.85rem !important;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .filter-select { flex-grow: 1; max-width: unset; }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .wk-card-header { flex-direction: column; align-items: stretch; }
        .filter-wrapper { flex-direction: column; align-items: stretch; }
        .header-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .header-actions .btn { flex-grow: 1; justify-content: center; font-size: 0.8rem;}

        /* Mobile Card Table */
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: flex;
            flex-direction: column;
            border-bottom: 2px solid var(--wk-border);
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
            color: var(--wk-muted);
            text-transform: uppercase;
        }
        .table-clean tbody td.mobile-card-head {
            background: var(--wk-bg);
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
        .wali-wrapper { justify-content: flex-end; }
        .td-wali-col { align-items: flex-end !important; }
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
                <div class="stat-desc">Kelas terdaftar</div>
            </div>
        </div>

        <!-- Sudah Ada Wali -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['kelasWithWali'] }}</div>
                <div class="stat-label">Sudah Ada Wali</div>
                <div class="stat-desc">Kelas dengan wali</div>
            </div>
        </div>

        <!-- Belum Ada Wali -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['kelasWithoutWali'] }}</div>
                <div class="stat-label">Belum Ada Wali</div>
                <div class="stat-desc text-warning">Perlu ditunjuk</div>
            </div>
        </div>

        <!-- Total Wali Kelas -->
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fas fa-school"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalWaliKelas'] }}</div>
                <div class="stat-label">Total Guru Aktif</div>
                <div class="stat-desc">Tenaga pendidik</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="wk-card">
        <div class="wk-card-header">
            <h5 class="wk-card-title">
                <i class="fas fa-user-tie text-purple"></i> Penunjukan Wali Kelas
            </h5>
            <div class="header-actions d-flex gap-2">
                <a href="{{ route('waka.wali-kelas.print', request()->query()) }}" class="btn btn-secondary text-white d-flex align-items-center gap-1" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
            </div>
        </div>

        <!-- Filters Form -->
        <form action="{{ route('waka.wali-kelas.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama kelas atau wali..." value="{{ request('search') }}">
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

                <select name="status" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Sudah Ada Wali</option>
                    <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Belum Ada Wali</option>
                </select>
                
                <button type="submit" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                
                @if(request()->hasAny(['search', 'jenjang', 'status']) || (request('tahun_ajaran_id') && request('tahun_ajaran_id') != $currentTahunAjaran?->id))
                    <a href="{{ route('waka.wali-kelas.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 8px;">
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
                        <th>Jenjang & Cabang</th>
                        <th>Jumlah Siswa</th>
                        <th>Penugasan Wali Kelas</th>
                        <th class="text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelasList as $kelas)
                    <tr>
                        <td class="mobile-card-head" data-label="Kelas">
                            <div class="kelas-info">
                                <span class="kelas-nama">{{ $kelas->nama_kelas }}</span>
                                <span class="kelas-kode mt-1 px-2">{{ $kelas->kode_kelas }}</span>
                            </div>
                        </td>
                        <td data-label="Lokasi">
                            @php
                                $jenjangClass = [
                                    'PAUD' => 'bg-jnj-paud',
                                    'SD' => 'bg-jnj-sd',
                                    'SMP' => 'bg-jnj-smp',
                                    'SMA' => 'bg-jnj-sma',
                                ][$kelas->jenjang] ?? 'bg-light text-dark';
                            @endphp
                            <div class="d-flex flex-column align-items-start gap-1">
                                <span class="badge {{ $jenjangClass }} badge-jnj">{{ $kelas->jenjang }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;"><i class="fas fa-building me-1"></i>{{ $kelas->cabang->nama_cabang ?? '-' }}</span>
                            </div>
                        </td>
                        <td data-label="Jumlah Siswa">
                            <span class="badge bg-label-info px-2 py-1">{{ $kelas->siswa_count }} siswa</span>
                        </td>
                        <td class="td-wali-col flex-column align-items-start" data-label="Wali Kelas">
                            @if($kelas->waliKelasAssignments->count() > 0)
                                <div class="d-flex flex-column gap-1">
                                @foreach($kelas->waliKelasAssignments as $assignment)
                                    <div class="wali-wrapper">
                                        <div class="wali-avatar">{{ substr($assignment->tenagaPendidik->nama_lengkap, 0, 2) }}</div>
                                        <div class="d-flex flex-column align-items-start line-height-sm">
                                            <span style="font-size: 0.85rem;" class="fw-medium text-dark">{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            @else
                                <span class="status-unassigned"><i class="fas fa-exclamation-circle me-1"></i> Belum ada wali kelas</span>
                            @endif
                        </td>
                        <td class="td-actions text-end" data-label="Aksi">
                            <div class="d-flex justify-content-end gap-1 action-btns">
                                <button type="button" class="btn btn-sm btn-purple text-white px-2 py-1 rounded" style="background-color: #8b5cf6;" title="Assign Wali Kelas" onclick="openAssignModal({{ $kelas->id }}, '{{ addslashes($kelas->nama_kelas) }}', {{ $kelas->waliKelasAssignments->first()->tenagaPendidik->id ?? 'null' }})">
                                    <i class="fas fa-user-tie"></i>
                                </button>
                                <a href="{{ route('waka.wali-kelas.show', $kelas) }}" class="btn btn-sm btn-info text-white px-2 py-1 rounded" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-user-tie fs-1 mb-3" style="color: #e2e8f0;"></i>
                                <h6 class="mb-1">Tidak Ada Data Kelas</h6>
                                <p class="small mb-0">Silakan buat kelas terlebih dahulu di menu Data Kelas.</p>
                                <a href="{{ route('waka.kelas.create') }}" class="btn btn-primary btn-sm mt-3 px-3 rounded-pill">
                                    <i class="fas fa-plus me-1"></i> Tambah Kelas
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kelasList->hasPages())
        <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap">
            <span class="text-muted small">Menampilkan {{ $kelasList->firstItem() ?? 0 }} - {{ $kelasList->lastItem() ?? 0 }} dari {{ $kelasList->total() }} kelas</span>
            <div class="mt-2 mt-sm-0">
                {{ $kelasList->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Modal Assign Wali Kelas --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top">
                <h5 class="modal-title fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="fas fa-user-tie text-purple"></i> Assign Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Nama Kelas</label>
                        <input type="text" id="kelasName" class="form-control bg-light text-dark fw-medium" readonly style="border: 1px dashed #cbd5e1; border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Pencarian Pendidik</label>
                        <div class="d-flex gap-2">
                            <div class="position-relative flex-grow-1">
                                <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
                                <input type="text" id="searchWali" class="form-control" placeholder="Ketik nama wali kelas..." style="padding-left: 36px; border-radius: 8px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Pilih Wali Kelas (Pilih 1)</label>
                        <div id="waliList" style="max-height: 280px; overflow-y: auto; border: 1px solid var(--wk-border); border-radius: 10px; padding: 6px; background: #fafafa;">
                            @if(count($waliKelasOptions) > 0)
                                @foreach($waliKelasOptions as $wk)
                                    @php
                                        $assignedKelasList = $wk->waliKelasAssignments ?? collect();
                                        $hasAssignments = $assignedKelasList->count() > 0;
                                    @endphp
                                    <div class="wali-option d-flex align-items-center gap-3"
                                         data-id="{{ $wk->id }}"
                                         data-name="{{ strtolower($wk->nama_lengkap) }}"
                                         style="padding: 12px 14px; border-radius: 8px; margin-bottom: 6px; cursor: pointer; transition: all 0.2s;">
                                        
                                        <input type="radio" name="wali_kelas_id" value="{{ $wk->id }}" style="cursor: pointer; transform: scale(1.2);">
                                        
                                        <div class="wali-avatar text-white" style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b21b6); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; flex-shrink: 0;">
                                            {{ substr($wk->nama_lengkap, 0, 2) }}
                                        </div>
                                        
                                        <div style="flex: 1; line-height: 1.2;">
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $wk->nama_lengkap }}</div>
                                            
                                            @if($hasAssignments)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($assignedKelasList as $assignment)
                                                        <span class="badge bg-label-primary px-2" style="font-size: 0.65rem;">Mengajar {{ $assignment->kelas->nama_kelas }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center p-4 text-muted small">
                                    Data tenaga pendidik belum tersedia.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0 border-0 d-flex align-items-start gap-2" style="font-size: 0.8rem; border-radius: 8px;">
                        <i class="fas fa-info-circle text-info mt-1"></i>
                        <div>
                            <strong>Multi-Kelas:</strong> Satu wali kelas bisa ditugaskan ke lebih dari satu kelas, namun tidak disarankan terlalu banyak.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" id="btnRemoveWali" class="btn btn-danger fw-medium d-flex align-items-center justify-content-center btn-responsive" style="border-radius: 8px;">
                        <span>Cabut Status Wali</span>
                    </button>
                    
                    <div class="d-flex gap-2 w-sm-auto">
                        <button type="button" class="btn btn-secondary fw-medium btn-responsive" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-purple text-white fw-medium d-flex align-items-center justify-content-center gap-2 btn-responsive" style="background-color: #8b5cf6; border-radius: 8px;">
                            <i class="fas fa-save"></i> <span>Simpan Pilihan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Wali Kelas --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-user-times fs-3 text-danger"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Cabut Wali Kelas?</h5>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Wali kelas dari <strong><span id="deleteKelasName"></span></strong> akan dicabut secara permanen. Kelas akan menjadi "Belum ada wali kelas".</p>
                
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-secondary fw-medium px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger fw-medium px-4 d-flex align-items-center gap-2" style="border-radius: 8px;">
                        <i class="fas fa-trash"></i> Cabut
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentKelasId = null;
let currentKelasName = '';

function openAssignModal(kelasId, kelasName, currentWaliId) {
    currentKelasId = kelasId;
    currentKelasName = kelasName;
    document.getElementById('kelasName').value = kelasName;
    document.getElementById('assignForm').action = `/waka/wali-kelas/${kelasId}/assign`;

    // Reset search
    document.getElementById('searchWali').value = '';

    // Set current wali if exists
    if (currentWaliId && currentWaliId !== 'null') {
        const radio = document.querySelector(`input[name="wali_kelas_id"][value="${currentWaliId}"]`);
        if (radio) {
            radio.checked = true;
            // scroll into view
            radio.scrollIntoView({ block: "center", behavior: "smooth" });
            document.getElementById('btnRemoveWali').style.display = 'inline-flex';
        } else {
            document.getElementById('btnRemoveWali').style.display = 'none';
        }
    } else {
        // Uncheck all
        document.querySelectorAll('input[name="wali_kelas_id"]').forEach(r => r.checked = false);
        document.getElementById('btnRemoveWali').style.display = 'none';
    }

    filterWaliList();

    const modal = new bootstrap.Modal(document.getElementById('assignModal'));
    modal.show();
}

// Search functionality
document.getElementById('searchWali').addEventListener('input', filterWaliList);

function filterWaliList() {
    const searchTerm = document.getElementById('searchWali').value.toLowerCase();
    const waliOptions = document.querySelectorAll('.wali-option');

    waliOptions.forEach(option => {
        const name = option.getAttribute('data-name');
        const matchSearch = searchTerm === '' || name.includes(searchTerm);

        option.classList.toggle('d-none', !matchSearch);
        if (matchSearch) {
            option.style.setProperty('display', 'flex', 'important');
        } else {
            option.style.setProperty('display', 'none', 'important');
        }
    });
}

// Click effect for whole row
document.querySelectorAll('.wali-option').forEach(option => {
    option.addEventListener('click', function(e) {
        if(e.target.tagName !== 'INPUT') {
            const radio = this.querySelector('input[type="radio"]');
            if(radio) radio.checked = true;
        }
    });
});

// Open confirm delete modal
document.getElementById('btnRemoveWali').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    document.getElementById('deleteKelasName').textContent = currentKelasName;

    const assignModal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
    if (assignModal) {
        assignModal.hide();
    }

    setTimeout(function() {
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        confirmModal.show();
    }, 300);
});

// Confirm delete button
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
    if (confirmModal) {
        confirmModal.hide();
    }

    // Uncheck all radios to signal removal
    document.querySelectorAll('input[name="wali_kelas_id"]').forEach(r => r.checked = false);

    // Submit form
    setTimeout(function() {
        document.getElementById('assignForm').submit();
    }, 300);
});
</script>
@endsection
