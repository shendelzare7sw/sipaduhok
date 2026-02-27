@extends('layouts.sneat')

@section('title', 'Data Guru Pengajar')
@section('page-title', 'Data Guru Pengajar')
@section('page-subtitle')
Kelola penugasan guru pengajar {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* Stats Cards with Gradient */
.stat-card-gradient {
    border-radius: 12px;
    padding: 24px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
}

.stat-card-gradient:hover {
    transform: translateY(-5px);
}

.stat-icon-bg {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 70px;
    opacity: 0.15;
    z-index: 1;
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 38px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1.2;
}

.stat-desc {
    font-size: 13px;
    opacity: 0.8;
}

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

/* Filter Section */
.filter-section {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 24px;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-box input {
     padding-left: 50px !important;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    z-index: 10;
}

/* Guru Info */
.guru-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.guru-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

.guru-details {
    display: flex;
    flex-direction: column;
}

.guru-name {
    font-weight: 600;
    color: #111827;
    font-size: 15px;
}

.guru-nip {
    font-size: 12px;
    color: #6b7280;
    font-family: 'Monaco', 'Consolas', monospace;
}

/* Assignment Chips */
.assignment-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    max-width: 350px;
}

.assignment-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #f3f4f6;
    border-radius: 6px;
    font-size: 12px;
    color: #374151;
}

.assignment-chip .mapel {
    font-weight: 600;
    color: #0d9488;
}

.assignment-chip .kelas {
    color: #6b7280;
}

.no-assignment {
    color: #9ca3af;
    font-style: italic;
    font-size: 13px;
}

.more-assignments {
    font-size: 11px;
    color: #6b7280;
    background: #e5e7eb;
    padding: 2px 8px;
    border-radius: 10px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.3;
    color: #9ca3af;
}

.empty-state h3 {
    font-size: 18px;
    color: #6b7280;
    margin-bottom: 8px;
}

.empty-state p {
    color: #9ca3af;
    margin-bottom: 16px;
}

/* ── Mobile Responsive ── */
@media (max-width: 767.98px) {
    .search-box {
        width: 100% !important;
        min-width: unset !important;
    }
    .search-box input {
        width: 100% !important;
    }
    .filter-section {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .filter-section .form-select,
    .filter-section .btn {
        width: 100% !important;
    }
    .assignment-list {
        max-width: 100% !important;
    }
    /* Table → Card per row */
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
    }
    .table-card-mobile tbody tr:hover td { background: transparent !important; }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f3f4f6 !important;
        min-height: 44px;
        font-size: 13px;
    }
    .table-card-mobile tbody td.mobile-card-head {
        background: #f8fafc;
        padding: 14px;
        border-bottom: 2px solid #e5e7eb !important;
        justify-content: flex-start;
    }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        padding-right: 10px;
        min-width: 65px;
    }
    .table-card-mobile tbody td.mobile-card-full {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    .table-card-mobile tbody td.mobile-card-full::before { min-width: unset; }
    .table-card-mobile tbody td.mobile-card-actions {
        border-bottom: none !important;
        justify-content: flex-end;
    }
    .stat-number { font-size: 28px; }
    .stat-card-gradient { padding: 16px; }
}
</style>
@endsection

@section('content')
{{-- Stats Section --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-blue">
            <div class="stat-content">
                <div class="stat-title">Total Guru</div>
                <div class="stat-number">{{ $stats['totalGuru'] }}</div>
                <div class="stat-desc">Guru aktif</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-chalkboard-teacher"></i></div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-green">
            <div class="stat-content">
                <div class="stat-title">Sudah Ditugaskan</div>
                <div class="stat-number">{{ $stats['guruWithAssignment'] }}</div>
                <div class="stat-desc">Guru dengan penugasan</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-teal">
            <div class="stat-content">
                <div class="stat-title">Total Penugasan</div>
                <div class="stat-number">{{ $stats['totalPenugasan'] }}</div>
                <div class="stat-desc">Guru-Kelas-Mapel</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-tasks"></i></div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-purple">
            <div class="stat-content">
                <div class="stat-title">Mata Pelajaran</div>
                <div class="stat-number">{{ $stats['totalMataPelajaran'] }}</div>
                <div class="stat-desc">Mapel aktif</div>
            </div>
            <div class="stat-icon-bg"><i class="fas fa-book"></i></div>
        </div>
    </div>
</div>

{{-- Main Card --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="mb-1">
                <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Daftar Guru Pengajar
            </h5>
            <small class="text-muted">Penugasan guru otomatis dari Jadwal Pelajaran</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSinkronkan">
                <i class="fas fa-sync-alt me-1"></i> Sinkronkan dari Jadwal
            </button>
            <a href="{{ route('admin.guru-pengajar.print', request()->query()) }}"
               class="btn btn-primary"
               target="_blank">
                <i class="fas fa-print me-1"></i> Cetak
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- Filter Section --}}
        <form action="{{ route('admin.guru-pengajar.index') }}" method="GET">
            <div class="filter-section">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari nama, NIP, atau email..."
                           value="{{ request('search') }}">
                </div>

                <select name="tahun_ajaran_id" class="form-select" onchange="this.form.submit()" style="width: auto;">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select" onchange="this.form.submit()" style="width: auto;">
                    <option value="">Status: Aktif</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.guru-pengajar.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                       class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        @if($guruList->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-card-mobile">
                    <thead>
                        <tr>
                            <th>Guru Pengajar</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Penugasan (Kelas - Mapel)</th>
                            <th style="text-align: center; width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guruList as $guru)
                        <tr>
                            <td class="mobile-card-head">
                                <div class="guru-info">
                                    <div class="guru-avatar">{{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}</div>
                                    <div class="guru-details">
                                        <span class="guru-name">{{ $guru->nama_lengkap }}</span>
                                        <span class="guru-nip">{{ $guru->nip ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Telepon">{{ $guru->telepon ?? '-' }}</td>
                            <td data-label="Status">
                                @if($guru->user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-warning">Tidak Aktif</span>
                                @endif
                            </td>
                            <td data-label="Penugasan" class="mobile-card-full">
                                @if($guru->guruKelas->count() > 0)
                                    <div class="assignment-list">
                                        @foreach($guru->guruKelas->take(3) as $assignment)
                                            <div class="assignment-chip">
                                                <span class="kelas">{{ $assignment->kelas->nama_kelas }}</span>
                                                <span>-</span>
                                                <span class="mapel">{{ $assignment->mataPelajaran->nama_mapel }}</span>
                                            </div>
                                        @endforeach
                                        @if($guru->guruKelas->count() > 3)
                                            <span class="more-assignments">+{{ $guru->guruKelas->count() - 3 }} lainnya</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="no-assignment">Belum ada penugasan</span>
                                @endif
                            </td>
                            <td class="mobile-card-actions" style="text-align: center;">
                                <a href="{{ route('admin.guru-pengajar.show', ['guruPengajar' => $guru, 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                                   class="btn btn-sm btn-primary"
                                   title="Kelola Penugasan">
                                    <i class="fas fa-cog"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($guruList->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $guruList->firstItem() }} - {{ $guruList->lastItem() }} dari {{ $guruList->total() }} guru
                    </div>
                    <nav>
                        {{ $guruList->withQueryString()->links() }}
                    </nav>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Belum Ada Data Guru Pengajar</h3>
                <p>Penugasan guru pengajar dikelola otomatis dari <strong>Jadwal Pelajaran</strong>.<br>
                Pastikan jadwal pelajaran sudah dibuat dan guru sudah ditugaskan di jadwal tersebut,<br>
                lalu jalankan sinkronisasi untuk memperbarui data.</p>
                <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                    <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Buka Jadwal Pelajaran
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSinkronkan">
                        <i class="fas fa-sync-alt me-1"></i> Sinkronkan dari Jadwal
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Sinkronisasi --}}
<div class="modal fade" id="modalSinkronkan" tabindex="-1" aria-labelledby="modalSinkronkanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title" id="modalSinkronkanLabel">
                    <i class="fas fa-sync-alt text-warning me-2"></i>Konfirmasi Sinkronisasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3 align-items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1"><strong>Apakah Anda yakin ingin menyinkronkan ulang?</strong></p>
                        <p class="text-muted mb-0 small">Tindakan ini akan memperbarui semua penugasan guru berdasarkan jadwal pelajaran yang aktif. Penugasan yang tidak terdapat di jadwal akan dihapus.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form action="{{ route('admin.guru-pengajar.rebuild') }}" method="POST" style="display: inline;">
                    @csrf
                    @if(request('tahun_ajaran_id'))
                        <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">
                    @endif
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sync-alt me-1"></i>Ya, Sinkronkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
