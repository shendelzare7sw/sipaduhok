@extends('layouts.sneat')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Kelola Jadwal Pelajaran')
@section('page-subtitle')
    Susun jadwal mengajar untuk {{ $currentTahunAjaran ? $currentTahunAjaran->nama_tahun_ajaran : 'semua tahun ajaran' }}
@endsection

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@vite(['resources/css/waka/jadwal-pelajaran/index.css'])
@endsection

@section('content')
{{-- Stats Row --}}
<div class="stat-row">
    <div class="stat-widget">
        <div class="stat-icon stat-icon-primary">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value">{{ $stats['totalJadwal'] }}</div>
            <div class="stat-label">Total Jadwal</div>
            <div class="stat-desc">Jadwal aktif</div>
        </div>
    </div>
    <div class="stat-widget">
        <div class="stat-icon stat-icon-warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value">{{ $stats['jadwalKosong'] }}</div>
            <div class="stat-label">Jadwal Kosong</div>
            <div class="stat-desc text-warning">Menunggu guru</div>
        </div>
    </div>
    <div class="stat-widget">
        <div class="stat-icon stat-icon-success">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value">{{ $stats['totalGuru'] }}</div>
            <div class="stat-label">Guru Mengajar</div>
            <div class="stat-desc">Guru aktif</div>
        </div>
    </div>
    <div class="stat-widget">
        <div class="stat-icon stat-icon-purple">
            <i class="fas fa-school"></i>
        </div>
        <div class="stat-details">
            <div class="stat-value">{{ $stats['totalKelas'] }}</div>
            <div class="stat-label">Total Kelas</div>
            <div class="stat-desc">Kelas aktif</div>
        </div>
    </div>
</div>

    {{-- Main Card --}}
    <div class="jp-card">
        <div class="jp-card-header">
            <div>
                <h5 class="jp-card-title">
                    <i class="fas fa-calendar-week jp-title-icon"></i> Daftar Jadwal Pelajaran
                </h5>
                <div class="jp-card-subtitle">Kelola dan atur jadwal mengajar untuk setiap kelas</div>
            </div>
            <div class="btn-scroll-mobile">
                <a href="{{ route('waka.jadwal-pelajaran.create', request()->query()) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Tambah
                </a>
                <a href="{{ route('waka.pengaturan-istirahat.index') }}" class="btn btn-warning btn-sm text-white">
                    <i class="fas fa-coffee me-1"></i> Istirahat
                </a>
                
                <div class="dropdown d-inline-block">
                    <button class="btn btn-info btn-sm text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-tools me-1"></i> Aksi
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                            <i class="fas fa-copy me-2 text-info"></i> Duplikasi Jadwal</button></li>
                        <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#bulkReplaceModal">
                            <i class="fas fa-random me-2 text-secondary"></i> Ganti Semua Guru</button></li>
                        <li><a class="dropdown-item" href="{{ route('waka.jadwal-pelajaran.import') }}">
                            <i class="fas fa-file-import me-2 text-success"></i> Import Excel</a></li>
                    </ul>
                </div>

                <div class="dropdown d-inline-block">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-print me-1"></i> Cetak/Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item" href="{{ route('waka.jadwal-pelajaran.export-pdf', request()->query()) }}" target="_blank">
                            <i class="fas fa-file-pdf me-2 text-danger"></i> Export PDF (Semua)</a></li>
                        <li><a class="dropdown-item" href="{{ route('waka.jadwal-pelajaran.export-excel', request()->query()) }}">
                            <i class="fas fa-file-excel me-2 text-success"></i> Export Excel (Semua)</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#cetakKelasModal">
                            <i class="fas fa-id-card me-2 text-dark"></i> Cetak Per Kelas</button></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <form action="{{ route('waka.jadwal-pelajaran.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
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
                        <option value="KB" {{ request('jenjang') == 'KB' ? 'selected' : '' }}>KB</option>
                        <option value="TKA" {{ request('jenjang') == 'TKA' ? 'selected' : '' }}>TKA</option>
                        <option value="TKB" {{ request('jenjang') == 'TKB' ? 'selected' : '' }}>TKB</option>
                        <option value="SD" {{ request('jenjang') == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ request('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ request('jenjang') == 'SMA' ? 'selected' : '' }}>SMA</option>
                    </select>

                    <select name="kelas_id" class="form-select filter-select" data-auto-submit>
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kls)
                            <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                                {{ $kls->nama_kelas }} - {{ $kls->cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>

                    <select name="guru_id" class="form-select filter-select" data-auto-submit>
                        <option value="">Semua Guru</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>

                @if(request()->hasAny(['jenjang', 'kelas_id', 'guru_id']))
                    <a href="{{ route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                        class="btn btn-outline-danger btn-sm px-3 jp-radius-sm">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Jadwal Table --}}
        @if($jadwalList->count() > 0)
            <form id="bulk-form-jadwal">
                @csrf
                <div class="bulk-action-bar">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2 d-md-none mobile-select-bar">
                                <input type="checkbox" id="mobile-select-all-jadwal" class="form-check-input"
                                    data-mobile-toggle-select-all>
                                <label for="mobile-select-all-jadwal" class="mb-0 small fw-semibold text-secondary jp-cursor-pointer">
                                    Semua
                                </label>
                            </div>
                            <div id="selectedInfo" class="selected-badge d-none">
                                <i class="fas fa-check-circle me-1"></i>
                                <span id="selectedCount">0</span> <span class="d-none d-sm-inline">Jadwal</span> Terpilih
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger btn-sm text-white px-3 d-none" id="bulkDeleteBtn" data-bulk-delete-trigger>
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                            <button type="button" class="btn btn-warning text-white btn-sm text-white px-3 d-none" id="bulkStatusBtn" data-bulk-status-trigger>
                                <i class="fas fa-sync me-1"></i> Status
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                        <table class="table table-clean">
                            <thead>
                                <tr>
                                    <th class="jp-col-check">
                                        <input type="checkbox" id="select-all-jadwal" class="form-check-input"
                                            data-toggle-select-all-jadwal>
                                    </th>
                                    <th class="jp-col-day">Hari</th>
                                    <th class="jp-col-time">Jam</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
                                    <th class="jp-col-status">Status</th>
                                    <th class="jp-col-actions">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalList as $jadwal)
                                    <tr>
                                        <td class="text-center align-middle mobile-card-checkbox">
                                            <input type="checkbox" name="jadwal_ids[]" value="{{ $jadwal->id }}"
                                                class="form-check-input jadwal-checkbox"
                                                data-mapel="{{ $jadwal->mataPelajaran->nama_mapel }}"
                                                data-kelas="{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}"
                                                data-hari="{{ $jadwal->hari }}"
                                                data-jam="{{ $jadwal->jam_mulai->format('H:i') }}-{{ $jadwal->jam_selesai->format('H:i') }}">
                                        </td>
                                        <td class="mobile-card-head">
                                            <span class="hari-badge hari-{{ strtolower($jadwal->hari) }}">
                                                {{ $jadwal->hari }}
                                            </span>
                                        </td>
                                        <td data-label="Jam">
                                            <span class="jam-badge">
                                                {{ $jadwal->jam_mulai->format('H:i') }} -
                                                {{ $jadwal->jam_selesai->format('H:i') }}
                                            </span>
                                        </td>
                                        <td data-label="Kelas">
                                            <div class="kelas-chip">
                                                <i class="fas fa-school"></i>
                                                <span>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Mapel">
                                            <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                        </td>
                                        <td data-label="Guru">
                                            @if($jadwal->guru)
                                                <div class="guru-info">
                                                    <div class="guru-avatar-sm">
                                                        {{ strtoupper(substr($jadwal->guru->nama_lengkap, 0, 1)) }}
                                                    </div>
                                                    <span class="guru-name">{{ $jadwal->guru->nama_lengkap }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">Belum ditugaskan</span>
                                            @endif
                                        </td>
                                        <td data-label="Status">
                                            @if($jadwal->status == 'aktif')
                                                <span class="status-badge status-aktif">Aktif</span>
                                            @else
                                                <span class="status-badge status-kosong">Kosong</span>
                                            @endif
                                        </td>
                                        <td class="mobile-card-actions text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#gantiGuruModal{{ $jadwal->id }}" title="Ganti Guru">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                                <a href="{{ route('waka.jadwal-pelajaran.edit', $jadwal) }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $jadwal->id }}" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </form>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-week"></i>
                <h3>Belum Ada Jadwal Pelajaran</h3>
                <p>Silakan tambahkan jadwal pelajaran untuk tahun ajaran ini.</p>
                <a href="{{ route('waka.jadwal-pelajaran.create', request()->query()) }}" class="btn btn-primary btn-sm mt-3 px-3 rounded-pill">
                    <i class="fas fa-plus me-1"></i> Tambah Jadwal Pelajaran
                </a>
            </div>
        @endif
    </div>

    {{-- Modals untuk Delete dan Ganti Guru --}}
    @foreach($jadwalList as $jadwal)
        {{-- Modal Delete --}}
        <div class="modal fade" id="deleteModal{{ $jadwal->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $jadwal->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered jp-dialog-xs">
                <div class="modal-content border-0 shadow jp-modal">
                    <div class="modal-header border-0 jp-modal-header jp-modal-header-danger">
                        <h6 class="modal-title fw-semibold mb-0 text-white" id="deleteModalLabel{{ $jadwal->id }}">
                            <i class="fas fa-trash-alt me-2 text-white"></i>Hapus Jadwal Pelajaran
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="d-flex align-items-start gap-3 p-3 mb-3 jp-info-box">
                            <div class="jp-icon-box jp-icon-box-danger">
                                <i class="fas fa-book text-danger jp-icon-sm"></i>
                            </div>
                            <div class="jp-min-w-0">
                                <div class="fw-semibold mb-1">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                <div class="text-muted jp-muted-copy">
                                    <div><i class="fas fa-school me-1"></i> {{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</div>
                                    <div><i class="fas fa-calendar-day me-1"></i> {{ $jadwal->hari }}, {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}</div>
                                    @if($jadwal->guru)
                                        <div><i class="fas fa-user-tie me-1"></i> {{ $jadwal->guru->nama_lengkap }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 px-1 jp-danger-copy">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Jadwal ini akan dihapus permanen dan tidak dapat dipulihkan.</span>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
                        <button type="button" class="btn btn-light fw-semibold px-4 jp-radius-sm" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <form action="{{ route('waka.jadwal-pelajaran.destroy', $jadwal) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger fw-semibold px-4 text-white jp-radius-sm">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Ganti Guru --}}
        <div class="modal fade" id="gantiGuruModal{{ $jadwal->id }}" tabindex="-1"
            aria-labelledby="gantiGuruModalLabel{{ $jadwal->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered jp-dialog-md">
                <form action="{{ route('waka.jadwal-pelajaran.ganti-guru', $jadwal) }}" method="POST" class="jp-form-full">
                    @csrf
                    <input type="hidden" name="guru_id_baru" id="guruIdBaru{{ $jadwal->id }}" value="">
                    <div class="modal-content border-0 shadow jp-modal">
                        <div class="modal-header border-0 jp-modal-header jp-modal-header-info">
                            <h6 class="modal-title fw-semibold mb-0 text-white" id="gantiGuruModalLabel{{ $jadwal->id }}">
                                <i class="fas fa-exchange-alt me-2 text-white"></i>Ganti Guru &middot; {{ $jadwal->mataPelajaran->nama_mapel }}
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Informasi Jadwal</label>
                                <div class="jp-schedule-info">
                                    <div><i
                                            class="fas fa-calendar-day me-2 text-primary"></i><strong>{{ $jadwal->hari }}</strong>,
                                        {{ $jadwal->jam_mulai->format('H:i') }} -
                                        {{ $jadwal->jam_selesai->format('H:i') }}
                                    </div>
                                    <div class="mt-1"><i
                                            class="fas fa-school me-2 text-success"></i>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Guru Saat Ini</label>
                                <input type="text" class="form-control bg-light" readonly
                                    value="{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : 'Belum ada guru' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Guru Baru <small class="text-muted fw-normal">(Kosongkan
                                        untuk set jadwal kosong)</small></label>

                                {{-- Search Input --}}
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="searchGuru{{ $jadwal->id }}"
                                        placeholder="Cari nama guru..." data-guru-search data-jadwal-id="{{ $jadwal->id }}">
                                </div>

                                {{-- Guru Display Selected --}}
                                <div id="selectedGuruDisplay{{ $jadwal->id }}" class="mb-2 d-none jp-selected-guru">
                                    <div class="jp-flex-between">
                                        <div class="jp-flex-center-gap">
                                            <i class="fas fa-user-check text-success"></i>
                                            <span id="selectedGuruName{{ $jadwal->id }}" class="jp-selected-guru-name"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-clear-guru data-jadwal-id="{{ $jadwal->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Guru List --}}
                                <div id="guruList{{ $jadwal->id }}" class="jp-guru-list">
                                    <div class="guru-opt-item guru-opt-item-empty"
                                        data-select-guru data-jadwal-id="{{ $jadwal->id }}" data-guru-id="" data-guru-name="Kosongkan (Menunggu Guru)">
                                        <i class="fas fa-user-slash text-warning me-2"></i>
                                        <span class="guru-opt-empty-text">-- Kosongkan (Menunggu Guru) --</span>
                                    </div>
                                    @foreach($guruList as $guru)
                                        <div class="guru-opt-item guru-opt-item-row {{ $jadwal->guru_id == $guru->id ? 'guru-opt-item-disabled' : '' }}" data-name="{{ strtolower($guru->nama_lengkap) }}"
                                            data-id="{{ $guru->id }}" data-jadwal="{{ $jadwal->id }}"
                                            data-select-guru data-jadwal-id="{{ $jadwal->id }}" data-guru-id="{{ $guru->id }}" data-guru-name="{{ $guru->nama_lengkap }}">
                                            <div class="guru-option-avatar">
                                                {{ substr($guru->nama_lengkap, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="guru-option-name">{{ $guru->nama_lengkap }}</div>
                                                <div class="guru-option-cabang">
                                                    {{ $guru->user->cabang->nama_cabang ?? '-' }}</div>
                                            </div>
                                            @if($jadwal->guru_id == $guru->id)
                                                <span class="badge bg-secondary ms-auto">Saat Ini</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alasan Penggantian <small
                                        class="text-muted fw-normal">(Opsional)</small></label>
                                <textarea name="alasan" class="form-control" rows="2"
                                    placeholder="Contoh: Guru resign, Guru mutasi, Penyesuaian jadwal, dll"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
                            <button type="button" class="btn btn-light fw-semibold px-4 jp-radius-sm" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-info fw-semibold px-4 text-white jp-radius-sm">
                                <i class="fas fa-exchange-alt me-1"></i> Ganti Guru
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endforeach

    {{-- Modal Bulk Replace Guru --}}
    <div class="modal fade" id="bulkReplaceModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('waka.jadwal-pelajaran.bulk-replace-guru') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id"
                    value="{{ request('tahun_ajaran_id', $currentTahunAjaran?->id) }}">
                <div class="modal-content border-0 shadow jp-modal">
                    <div class="modal-header border-0 jp-modal-header jp-modal-header-purple">
                        <h6 class="modal-title fw-semibold mb-0 text-white">
                            <i class="fas fa-random me-2 text-white"></i>Ganti Semua Jadwal Guru
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Fitur ini akan mengganti SEMUA jadwal dari guru lama ke guru baru dalam satu tahun ajaran.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Lama <span class="text-danger">*</span></label>
                            <select name="guru_id_lama" class="form-select" required>
                                <option value="">-- Pilih Guru Lama --</option>
                                @foreach($guruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Baru <small class="text-muted">(Kosongkan untuk set semua jadwal
                                    kosong)</small></label>
                            <select name="guru_id_baru" class="form-select">
                                <option value="">-- Kosongkan Semua Jadwal --</option>
                                @foreach($guruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penggantian <span class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control" rows="3"
                                placeholder="Contoh: Guru resign, Guru mutasi, dll" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3 gap-2">
                        <button type="button" class="btn btn-light fw-semibold jp-radius-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-semibold jp-radius-sm">
                            <i class="fas fa-random me-1"></i> Ganti Semua Jadwal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Duplicate Jadwal --}}
    <div class="modal fade" id="duplicateModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('waka.jadwal-pelajaran.duplicate') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow jp-modal">
                    <div class="modal-header border-0 jp-modal-header jp-modal-header-success">
                        <h6 class="modal-title fw-semibold mb-0 text-white">
                            <i class="fas fa-copy me-2 text-white"></i>Duplikasi Jadwal
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Fitur ini akan meng-copy semua jadwal dari tahun ajaran lama ke tahun ajaran baru yang dipilih.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Sumber <span class="text-danger">*</span></label>
                            <select name="tahun_ajaran_id_lama" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran Lama --</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jadwal akan di-copy dari tahun ajaran ini</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran Tujuan <span class="text-danger">*</span></label>
                            <select name="tahun_ajaran_id_baru" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran Baru --</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ $currentTahunAjaran && $currentTahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jadwal akan di-copy ke tahun ajaran ini</small>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Jadwal hanya akan di-copy untuk kelas yang memiliki nama dan jenjang
                            yang sama.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-3 px-3 gap-2">
                        <button type="button" class="btn btn-light fw-semibold jp-radius-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-semibold text-white jp-radius-sm">
                            <i class="fas fa-copy me-1"></i> Duplikasi Jadwal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Bulk Delete --}}
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered jp-dialog-sm">
            <div class="modal-content border-0 shadow jp-modal">
                <div class="modal-header border-0 jp-modal-header jp-modal-header-danger">
                    <h6 class="modal-title fw-semibold mb-0 text-white" id="bulkDeleteModalLabel">
                        <i class="fas fa-trash-alt me-2 text-white"></i>
                        Hapus Massal &middot; <span class="badge jp-header-badge" id="bulkDeleteCount">0</span> jadwal dipilih
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-muted mb-3 jp-small-copy">Jadwal berikut akan dihapus secara permanen:</p>
                    <div id="bulkDeleteList" class="jp-scroll-column">
                        {{-- Diisi oleh JavaScript --}}
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 px-1 jp-danger-copy">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Tindakan ini tidak dapat dibatalkan!</span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
                    <button type="button" class="btn btn-light fw-semibold px-4 jp-radius-sm" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <form id="bulk-delete-form" action="{{ route('waka.jadwal-pelajaran.bulk-delete') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="jadwal_ids" id="bulk-delete-ids">
                        <button type="submit" class="btn btn-danger fw-semibold px-4 text-white jp-radius-sm">
                            <i class="fas fa-trash me-1"></i> Hapus Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bulk Update Status --}}
    <div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered jp-dialog-xs">
            <div class="modal-content border-0 shadow jp-modal">
                <div class="modal-header border-0 jp-modal-header jp-modal-header-warning">
                    <h6 class="modal-title fw-semibold mb-0 text-white" id="bulkStatusModalLabel">
                        <i class="fas fa-toggle-on me-2 text-white"></i>
                        Ubah Status Massal &middot; <span id="bulkStatusCount">0</span> jadwal
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-muted mb-3 jp-small-copy">Pilih status baru untuk jadwal yang dipilih:</p>
                    <div class="d-flex gap-3 mb-3">
                        <label class="d-flex align-items-center gap-2 p-3 rounded flex-fill jp-status-option-active">
                            <input class="form-check-input mt-0" type="radio" name="status_choice" id="statusAktif" value="aktif" checked>
                            <span class="fw-semibold text-success">AKTIF</span>
                            <i class="fas fa-circle-check text-success ms-auto"></i>
                        </label>
                        <label class="d-flex align-items-center gap-2 p-3 rounded flex-fill jp-status-option-empty">
                            <input class="form-check-input mt-0" type="radio" name="status_choice" id="statusKosong" value="kosong">
                            <span class="fw-semibold text-secondary">KOSONG</span>
                            <i class="fas fa-circle text-secondary ms-auto"></i>
                        </label>
                    </div>
                    <div class="d-flex align-items-center gap-2 p-3 jp-note-box">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span class="text-muted">Status akan diubah untuk semua jadwal yang telah Anda pilih.</span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
                    <button type="button" class="btn btn-light fw-semibold px-4 jp-radius-sm" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-warning fw-semibold px-4 text-white jp-radius-sm" data-submit-bulk-status>
                        <i class="fas fa-toggle-on me-1"></i> Ubah Status
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="jp-config"
        data-bulk-update-status-url="{{ route('waka.jadwal-pelajaran.bulk-update-status') }}"
        data-bulk-delete-url="{{ route('waka.jadwal-pelajaran.bulk-delete') }}"
        data-csrf-token="{{ csrf_token() }}"
        data-current-tahun-ajaran-id="{{ request('tahun_ajaran_id', $currentTahunAjaran?->id) }}"
        data-export-excel-base-url="{{ url('waka/jadwal-pelajaran/kelas') }}"
        data-print-base-url="{{ url('waka/jadwal-pelajaran/kelas') }}"></div>

    {{-- Modal Cetak Per Kelas --}}
    <div class="modal fade" id="cetakKelasModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow jp-modal">
                <div class="modal-header border-0 jp-modal-header jp-modal-header-dark">
                    <h6 class="modal-title fw-semibold mb-0 text-white">
                        <i class="fas fa-print me-2 text-white"></i>Cetak Jadwal Pelajaran
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="d-flex align-items-center gap-2 p-3 mb-3 jp-note-box">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span class="text-muted">Pilih kelas pada cabang Anda untuk mencetak jadwal spesifik.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select id="printKelasId" class="form-select">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($allKelasList as $kelas)
                                <option value="{{ $kelas->id }}" data-cabang="{{ $kelas->cabang_id }}">
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2 flex-wrap">
                    <button type="button" class="btn btn-light fw-semibold jp-radius-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger fw-semibold text-white jp-radius-sm" data-submit-cetak-kelas="pdf">
                        <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                    </button>
                    <button type="button" class="btn btn-success fw-semibold text-white jp-radius-sm" data-submit-cetak-kelas="excel">
                        <i class="fas fa-file-excel me-1"></i> Cetak Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @vite(['resources/js/waka/jadwal-pelajaran/index.js'])
@endsection

