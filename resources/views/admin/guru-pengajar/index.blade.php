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
    padding-left: 42px;
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
            <div class="stat-icon-bg">👩‍🏫</div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-green">
            <div class="stat-content">
                <div class="stat-title">Sudah Ditugaskan</div>
                <div class="stat-number">{{ $stats['guruWithAssignment'] }}</div>
                <div class="stat-desc">Guru dengan penugasan</div>
            </div>
            <div class="stat-icon-bg">✅</div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-teal">
            <div class="stat-content">
                <div class="stat-title">Total Penugasan</div>
                <div class="stat-number">{{ $stats['totalPenugasan'] }}</div>
                <div class="stat-desc">Guru-Kelas-Mapel</div>
            </div>
            <div class="stat-icon-bg">📝</div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card-gradient bg-gradient-purple">
            <div class="stat-content">
                <div class="stat-title">Mata Pelajaran</div>
                <div class="stat-number">{{ $stats['totalMataPelajaran'] }}</div>
                <div class="stat-desc">Mapel aktif</div>
            </div>
            <div class="stat-icon-bg">📖</div>
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
            <small class="text-muted">Kelola penugasan guru ke kelas dan mata pelajaran</small>
        </div>
        <div>
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
                <table class="table table-hover">
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
                            <td>
                                <div class="guru-info">
                                    <div class="guru-avatar">{{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}</div>
                                    <div class="guru-details">
                                        <span class="guru-name">{{ $guru->nama_lengkap }}</span>
                                        <span class="guru-nip">{{ $guru->nip ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $guru->telepon ?? '-' }}</td>
                            <td>
                                @if($guru->user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-warning">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
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
                            <td style="text-align: center;">
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
                <p>Silakan tambah tenaga pendidik dengan role "Guru Pengajar" terlebih dahulu.</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-users me-1"></i> Kelola User
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
