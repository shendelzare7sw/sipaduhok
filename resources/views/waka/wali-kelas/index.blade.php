@extends('layouts.sneat')

@section('title', 'Data Wali Kelas')

@section('page-title', 'Data Wali Kelas')
@section('page-subtitle')
Kelola penunjukan wali kelas di cabang Anda {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/wali-kelas/index.css'])
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <!-- Total Kelas -->
        <div class="stat-widget">
            <div class="stat-icon stat-icon-total">
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
            <div class="stat-icon stat-icon-assigned">
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
            <div class="stat-icon stat-icon-unassigned">
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
            <div class="stat-icon stat-icon-teachers">
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
                
                <select name="tahun_ajaran_id" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                
                <select name="jenjang" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $j)
                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Status</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Sudah Ada Wali</option>
                    <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Belum Ada Wali</option>
                </select>
                
                <button type="submit" class="btn btn-secondary btn-sm px-3 filter-action-btn">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                
                @if(request()->hasAny(['search', 'jenjang', 'status']) || (request('tahun_ajaran_id') && request('tahun_ajaran_id') != $currentTahunAjaran?->id))
                    <a href="{{ route('waka.wali-kelas.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}" class="btn btn-outline-danger btn-sm px-3 filter-action-btn">
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
                                <span class="text-muted location-text"><i class="fas fa-building me-1"></i>{{ $kelas->cabang->nama_cabang ?? '-' }}</span>
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
                                            <span class="fw-medium text-dark assigned-wali-name">{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
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
                                <button type="button" class="btn btn-sm btn-purple text-white px-2 py-1 rounded assign-action-btn" title="Assign Wali Kelas" data-assign-wali data-kelas-id="{{ $kelas->id }}" data-kelas-name="{{ $kelas->nama_kelas }}" data-current-wali-id="{{ $kelas->waliKelasAssignments->first()->tenagaPendidik->id ?? '' }}">
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
                                <i class="fas fa-user-tie fs-1 mb-3 empty-wali-icon"></i>
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
        <div class="modal-content border-0 shadow assign-modal-content">
            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top">
                <h5 class="modal-title fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="fas fa-user-tie text-purple"></i> Assign Wali Kelas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Nama Kelas</label>
                        <input type="text" id="kelasName" class="form-control bg-light text-dark fw-medium dashed-input" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Pencarian Pendidik</label>
                        <div class="d-flex gap-2">
                            <div class="position-relative flex-grow-1">
                                <i class="fas fa-search position-absolute text-muted modal-search-icon"></i>
                                <input type="text" id="searchWali" class="form-control modal-search-input" placeholder="Ketik nama wali kelas...">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Pilih Wali Kelas (Pilih 1)</label>
                        <div id="waliList" class="wali-list">
                            @if(count($waliKelasOptions) > 0)
                                @foreach($waliKelasOptions as $wk)
                                    @php
                                        $assignedKelasList = $wk->waliKelasAssignments ?? collect();
                                        $hasAssignments = $assignedKelasList->count() > 0;
                                    @endphp
                                    <div class="wali-option d-flex align-items-center gap-3"
                                         data-id="{{ $wk->id }}"
                                         data-name="{{ strtolower($wk->nama_lengkap) }}"
                                         >
                                        
                                        <input type="radio" name="wali_kelas_id" value="{{ $wk->id }}" class="wali-radio">
                                        
                                        <div class="wali-avatar text-white modal-wali-avatar">
                                            {{ substr($wk->nama_lengkap, 0, 2) }}
                                        </div>
                                        
                                        <div class="modal-wali-info">
                                            <div class="fw-bold text-dark modal-wali-name">{{ $wk->nama_lengkap }}</div>
                                            <div class="text-muted modal-wali-branch"><i class="fas fa-building me-1"></i>{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                            
                                            @if($hasAssignments)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($assignedKelasList as $assignment)
                                                        @if($assignment->kelas)
                                                            <span class="badge bg-primary text-white px-2 modal-kelas-badge">Mengajar {{ $assignment->kelas->nama_kelas }}</span>
                                                        @endif
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

                    <div class="alert alert-info py-2 px-3 mb-0 border-0 d-flex align-items-start gap-2 info-alert-small">
                        <i class="fas fa-info-circle text-info mt-1"></i>
                        <div>
                            <strong>Multi-Kelas:</strong> Satu wali kelas bisa ditugaskan ke lebih dari satu kelas, namun tidak disarankan terlalu banyak.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" id="btnRemoveWali" class="btn btn-danger fw-medium d-flex align-items-center justify-content-center btn-responsive text-white rounded-action-btn is-hidden">
                        <span>Cabut Status Wali</span>
                    </button>
                    
                    <div class="d-flex gap-2 w-sm-auto">
                        <button type="button" class="btn btn-secondary fw-medium btn-responsive rounded-action-btn" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple text-white fw-medium d-flex align-items-center justify-content-center gap-2 btn-responsive assign-action-btn rounded-action-btn">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-label-danger d-inline-flex align-items-center justify-content-center delete-icon-circle">
                        <i class="fas fa-user-times fs-3 text-danger"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Cabut Wali Kelas?</h5>
                <p class="text-muted mb-3 delete-message">Wali kelas dari <strong><span id="deleteKelasName"></span></strong> akan dicabut secara permanen. Kelas akan menjadi "Belum ada wali kelas".</p>
                
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-secondary fw-medium px-4 rounded-action-btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger fw-medium px-4 d-flex align-items-center gap-2 text-white rounded-action-btn">
                        <i class="fas fa-trash"></i> Cabut
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/waka/wali-kelas/index.js'])
@endsection
