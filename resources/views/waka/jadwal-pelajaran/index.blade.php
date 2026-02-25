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
    <style>
        /* Stats Cards dengan Gradient */
        .stat-card-gradient {
            border-radius: 12px;
            padding: 24px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        .bg-gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-orange {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 24px;
        }

        /* Jadwal Table */
        .jadwal-table {
            font-size: 14px;
        }

        .jadwal-table th {
            background: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 12px;
        }

        .jadwal-table td {
            padding: 12px;
            vertical-align: middle;
        }

        .hari-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        .hari-senin {
            background: #dbeafe;
            color: #1e40af;
        }

        .hari-selasa {
            background: #dcfce7;
            color: #166534;
        }

        .hari-rabu {
            background: #fef3c7;
            color: #92400e;
        }

        .hari-kamis {
            background: #fce7f3;
            color: #9f1239;
        }

        .hari-jumat {
            background: #e0e7ff;
            color: #3730a3;
        }

        .hari-sabtu {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .jam-badge {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
        }

        .kelas-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #f3f4f6;
            border-radius: 6px;
            font-size: 13px;
        }

        .kelas-chip i {
            color: #0d9488;
        }

        .guru-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .guru-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }

        .guru-name {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .status-kosong {
            background: #fee2e2;
            color: #991b1b;
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

        /* Action Buttons */
        .btn-action-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Modal Centering Fix */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
        }

        @media (min-width: 576px) {
            .modal-dialog-centered {
                min-height: calc(100% - 3.5rem);
            }
        }
    </style>
@endsection

@section('content')
{{-- Success/Error Messages --}}
@section('content')
    {{-- Success/Error Messages --}}

    {{-- Stats Section --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card-gradient bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Jadwal</div>
                    <div class="stat-number">{{ $stats['totalJadwal'] }}</div>
                    <div class="stat-desc">Jadwal aktif</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-calendar"></i></div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card-gradient bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Jadwal Kosong</div>
                    <div class="stat-number">{{ $stats['jadwalKosong'] }}</div>
                    <div class="stat-desc">Menunggu guru</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card-gradient bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Guru Mengajar</div>
                    <div class="stat-number">{{ $stats['totalGuru'] }}</div>
                    <div class="stat-desc">Guru aktif</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card-gradient bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Total Kelas</div>
                    <div class="stat-number">{{ $stats['totalKelas'] }}</div>
                    <div class="stat-desc">Kelas aktif</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-school"></i></div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="mb-1">
                    <i class="fas fa-calendar-week text-primary me-2"></i>Daftar Jadwal Pelajaran
                </h5>
                <small class="text-muted">Kelola dan atur jadwal mengajar untuk setiap kelas</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('waka.pengaturan-istirahat.index') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-coffee me-1"></i> Atur Waktu Istirahat
                </a>
                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                    <i class="fas fa-copy me-1"></i> Duplikasi Jadwal
                </button>
                <a href="{{ route('waka.jadwal-pelajaran.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm"
                    target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('waka.jadwal-pelajaran.export-excel', request()->query()) }}"
                    class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#cetakKelasModal">
                    <i class="fas fa-print me-1"></i> Cetak Jadwal Kelas
                </button>
                <a href="{{ route('waka.jadwal-pelajaran.import') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-import me-1"></i> Import Excel
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#bulkReplaceModal">
                    <i class="fas fa-exchange-alt me-1"></i> Ganti Guru Massal
                </button>
                <a href="{{ route('waka.jadwal-pelajaran.create', request()->query()) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Jadwal
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Filter Section --}}
            <form action="{{ route('waka.jadwal-pelajaran.index') }}" method="GET">
                <div class="filter-section">
                    <select name="tahun_ajaran_id" class="form-select" onchange="this.form.submit()" style="width: auto;">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <select name="jenjang" class="form-select" onchange="this.form.submit()" style="width: auto;">
                        <option value="">Semua Jenjang</option>
                        <option value="KB" {{ request('jenjang') == 'KB' ? 'selected' : '' }}>KB</option>
                        <option value="TKA" {{ request('jenjang') == 'TKA' ? 'selected' : '' }}>TKA</option>
                        <option value="TKB" {{ request('jenjang') == 'TKB' ? 'selected' : '' }}>TKB</option>
                        <option value="SD" {{ request('jenjang') == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ request('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ request('jenjang') == 'SMA' ? 'selected' : '' }}>SMA</option>
                    </select>

                    <select name="kelas_id" class="form-select" onchange="this.form.submit()" style="width: auto;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kls)
                            <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                                {{ $kls->nama_kelas }} - {{ $kls->cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>

                    <select name="guru_id" class="form-select" onchange="this.form.submit()" style="width: auto;">
                        <option value="">Semua Guru</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['jenjang', 'kelas_id', 'guru_id']))
                        <a href="{{ route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Jadwal Table --}}
            @if($jadwalList->count() > 0)
                <form id="bulk-form-jadwal">
                    @csrf
                    <div class="mb-3 d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()" id="bulkDeleteBtn"
                            style="display: none;">
                            <i class="fas fa-trash me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="bulkUpdateStatus()" id="bulkStatusBtn"
                            style="display: none;">
                            <i class="fas fa-sync me-1"></i> Ubah Status Terpilih
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover jadwal-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="select-all-jadwal" class="form-check-input"
                                            onclick="toggleSelectAllJadwal()">
                                    </th>
                                    <th style="width: 80px;">Hari</th>
                                    <th style="width: 120px;">Jam</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
                                    <th style="width: 80px;">Status</th>
                                    <th style="width: 150px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalList as $jadwal)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="jadwal_ids[]" value="{{ $jadwal->id }}"
                                                class="form-check-input jadwal-checkbox" onchange="updateBulkButtons()">
                                        </td>
                                        <td>
                                            <span class="hari-badge hari-{{ strtolower($jadwal->hari) }}">
                                                {{ $jadwal->hari }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="jam-badge">
                                                {{ $jadwal->jam_mulai->format('H:i') }} -
                                                {{ $jadwal->jam_selesai->format('H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="kelas-chip">
                                                <i class="fas fa-school"></i>
                                                <span>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                        </td>
                                        <td>
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
                                        <td>
                                            @if($jadwal->status == 'aktif')
                                                <span class="status-badge status-aktif">Aktif</span>
                                            @else
                                                <span class="status-badge status-kosong">Kosong</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
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
                    <a href="{{ route('waka.jadwal-pelajaran.create', request()->query()) }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Tambah Jadwal Pelajaran
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modals untuk Delete dan Ganti Guru --}}
    @foreach($jadwalList as $jadwal)
        {{-- Modal Delete --}}
        <div class="modal fade" id="deleteModal{{ $jadwal->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $jadwal->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 550px; margin: 1.75rem auto;">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel{{ $jadwal->id }}">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Konfirmasi Hapus Jadwal
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus jadwal pelajaran:</p>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;">
                            <div style="font-weight: 600; font-size: 16px; color: #212529; margin-bottom: 8px;">
                                <i class="fas fa-book-open text-danger me-2"></i>
                                {{ $jadwal->mataPelajaran->nama_mapel }}
                            </div>
                            <div style="font-size: 13px; color: #6c757d; display: flex; flex-direction: column; gap: 4px;">
                                <div>
                                    <i class="fas fa-school me-1"></i> Kelas: <strong>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</strong>
                                </div>
                                <div>
                                    <i class="fas fa-calendar-day me-1"></i> Hari: <strong>{{ $jadwal->hari }}</strong>
                                </div>
                                <div>
                                    <i class="fas fa-clock me-1"></i> Jam:
                                    <strong>{{ $jadwal->jam_mulai->format('H:i') }} -
                                        {{ $jadwal->jam_selesai->format('H:i') }}</strong>
                                </div>
                                @if($jadwal->guru)
                                    <div>
                                        <i class="fas fa-user-tie me-1"></i> Guru:
                                        <strong>{{ $jadwal->guru->nama_lengkap }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p class="mt-3 mb-0">
                            <i class="fas fa-info-circle text-danger me-1"></i>
                            <small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <form action="{{ route('waka.jadwal-pelajaran.destroy', $jadwal) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Ganti Guru --}}
        <div class="modal fade" id="gantiGuruModal{{ $jadwal->id }}" tabindex="-1"
            aria-labelledby="gantiGuruModalLabel{{ $jadwal->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 600px; margin: 1.75rem auto;">
                <form action="{{ route('waka.jadwal-pelajaran.ganti-guru', $jadwal) }}" method="POST" style="width: 100%;">
                    @csrf
                    <input type="hidden" name="guru_id_baru" id="guruIdBaru{{ $jadwal->id }}" value="">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="gantiGuruModalLabel{{ $jadwal->id }}">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Ganti Guru - {{ $jadwal->mataPelajaran->nama_mapel }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Informasi Jadwal</label>
                                <div style="background: #f8f9fa; padding: 12px; border-radius: 6px; font-size: 13px;">
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
                                        placeholder="🔍 Cari nama guru..." oninput="filterGuruOptions({{ $jadwal->id }})">
                                </div>

                                {{-- Guru Display Selected --}}
                                <div id="selectedGuruDisplay{{ $jadwal->id }}" class="mb-2"
                                    style="display: none;
                                            background: #d1fae5; border: 1px solid #10b981; padding: 10px 12px; border-radius: 8px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <i class="fas fa-user-check text-success"></i>
                                            <span id="selectedGuruName{{ $jadwal->id }}" style="font-weight: 500;"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="clearGuruSelection{{ $jadwal->id }}()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Guru List --}}
                                <div id="guruList{{ $jadwal->id }}"
                                    style="max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
                                    <div class="guru-opt-item"
                                        style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f3f4f6;"
                                        onclick="selectGuruForJadwal({{ $jadwal->id }}, '', 'Kosongkan (Menunggu Guru)')"
                                        onmouseenter="this.style.background='#fef3c7'"
                                        onmouseleave="this.style.background='white'">
                                        <i class="fas fa-user-slash text-warning me-2"></i>
                                        <span style="color: #92400e;">-- Kosongkan (Menunggu Guru) --</span>
                                    </div>
                                    @foreach($guruList as $guru)
                                        <div class="guru-opt-item" data-name="{{ strtolower($guru->nama_lengkap) }}"
                                            data-id="{{ $guru->id }}" data-jadwal="{{ $jadwal->id }}"
                                            style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px;
                                                        {{ $jadwal->guru_id == $guru->id ? 'background: #e5e7eb; opacity: 0.6; pointer-events: none;' : '' }}"
                                            onclick="selectGuruForJadwal({{ $jadwal->id }}, {{ $guru->id }}, '{{ addslashes($guru->nama_lengkap) }}')"
                                            onmouseenter="this.style.background='#ecfdf5'"
                                            onmouseleave="this.style.background='{{ $jadwal->guru_id == $guru->id ? '#e5e7eb' : 'white' }}'">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                                {{ substr($guru->nama_lengkap, 0, 2) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: #111827;">{{ $guru->nama_lengkap }}</div>
                                                <div style="font-size: 11px; color: #6b7280;">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-exchange-alt me-1"></i> Ganti Guru
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function filterGuruOptions(jadwalId) {
                const searchInput = document.getElementById('searchGuru' + jadwalId);
                const searchTerm = searchInput.value.toLowerCase().trim();
                const guruList = document.querySelectorAll('#guruList' + jadwalId + ' .guru-opt-item[data-name]');

                guruList.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (searchTerm === '' || name.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            function selectGuruForJadwal(jadwalId, guruId, guruName) {
                document.getElementById('guruIdBaru' + jadwalId).value = guruId;
                const display = document.getElementById('selectedGuruDisplay' + jadwalId);
                const nameSpan = document.getElementById('selectedGuruName' + jadwalId);

                if (guruId === '' || guruName.includes('Kosongkan')) {
                    display.style.display = 'none';
                } else {
                    display.style.display = 'block';
                    nameSpan.textContent = guruName;
                }
            }

            function clearGuruSelection{{ $jadwal->id }}() {
                document.getElementById('guruIdBaru{{ $jadwal->id }}').value = '';
                document.getElementById('selectedGuruDisplay{{ $jadwal->id }}').style.display = 'none';
            }
        </script>
    @endforeach

    {{-- Modal Bulk Replace Guru --}}
    <div class="modal fade" id="bulkReplaceModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('waka.jadwal-pelajaran.bulk-replace-guru') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id"
                    value="{{ request('tahun_ajaran_id', $currentTahunAjaran?->id) }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ganti Semua Jadwal Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ganti Semua Jadwal</button>
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
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Duplikasi Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Duplikasi Jadwal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Bulk Delete --}}
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="bulkDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Konfirmasi Hapus Massal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Apakah Anda yakin ingin menghapus <strong><span id="bulkDeleteCount">0</span>
                            jadwal</strong> yang dipilih?</p>
                    <div
                        style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; border-radius: 6px; margin-bottom: 12px;">
                        <i class="fas fa-info-circle text-warning me-2"></i>
                        <small>Tindakan ini akan menghapus semua jadwal yang telah Anda pilih.</small>
                    </div>
                    <p class="mb-0">
                        <i class="fas fa-exclamation-circle text-danger me-1"></i>
                        <small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form id="bulk-delete-form" action="{{ route('waka.jadwal-pelajaran.bulk-delete') }}" method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="jadwal_ids" id="bulk-delete-ids">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Ya, Hapus Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bulk Update Status --}}
    <div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-labelledby="bulkStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="bulkStatusModalLabel">
                        <i class="fas fa-sync me-2"></i>
                        Ubah Status Massal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Ubah status untuk <strong><span id="bulkStatusCount">0</span> jadwal</strong> yang
                        dipilih:</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Status Baru:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_choice" id="statusAktif"
                                    value="aktif" checked>
                                <label class="form-check-label" for="statusAktif">
                                    <span class="badge bg-success">AKTIF</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_choice" id="statusKosong"
                                    value="kosong">
                                <label class="form-check-label" for="statusKosong">
                                    <span class="badge bg-secondary">KOSONG</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 12px; border-radius: 6px;">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <small>Status akan diubah untuk semua jadwal yang telah Anda pilih.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" onclick="submitBulkStatus()">
                        <i class="fas fa-sync me-1"></i> Ubah Status
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showToast(icon, title) {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        }

        // Toggle select all checkboxes
        function toggleSelectAllJadwal() {
            const selectAll = document.getElementById('select-all-jadwal');
            const checkboxes = document.querySelectorAll('.jadwal-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });

            updateBulkButtons();
        }

        // Update bulk action buttons visibility
        function updateBulkButtons() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
            const count = checkboxes.length;
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkStatusBtn = document.getElementById('bulkStatusBtn');
            const selectedCount = document.getElementById('selectedCount');

            if (count > 0) {
                bulkDeleteBtn.style.display = 'inline-block';
                bulkStatusBtn.style.display = 'inline-block';
                selectedCount.textContent = count;
            } else {
                bulkDeleteBtn.style.display = 'none';
                bulkStatusBtn.style.display = 'none';
            }

            // Update select all checkbox state
            const selectAll = document.getElementById('select-all-jadwal');
            const allCheckboxes = document.querySelectorAll('.jadwal-checkbox');
            selectAll.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
        }

        // Bulk delete function
        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                showToast('warning', 'Pilih minimal 1 jadwal untuk dihapus');
                return;
            }

            // Set count and IDs
            document.getElementById('bulkDeleteCount').textContent = ids.length;
            document.getElementById('bulk-delete-ids').value = JSON.stringify(ids);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
            modal.show();
        }

        // Bulk update status function - shows modal
        function bulkUpdateStatus() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                showToast('warning', 'Pilih minimal 1 jadwal untuk diubah statusnya');
                return;
            }

            // Set count in modal
            document.getElementById('bulkStatusCount').textContent = ids.length;

            // Store IDs temporarily in a data attribute
            document.getElementById('bulkStatusModal').setAttribute('data-selected-ids', JSON.stringify(ids));

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('bulkStatusModal'));
            modal.show();
        }

        // Submit bulk status update after modal confirmation
        function submitBulkStatus() {
            // Get stored IDs
            const idsJson = document.getElementById('bulkStatusModal').getAttribute('data-selected-ids');

            if (!idsJson) {
                showToast('error', 'Data tidak valid');
                return;
            }

            // Get selected status from radio buttons
            const statusRadio = document.querySelector('input[name="status_choice"]:checked');
            if (!statusRadio) {
                showToast('warning', 'Pilih status terlebih dahulu');
                return;
            }

            const status = statusRadio.value;

            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("waka.jadwal-pelajaran.bulk-update-status") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'jadwal_ids';
            idsInput.value = idsJson;
            form.appendChild(idsInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    {{-- Modal Cetak Per Kelas --}}
    <div class="modal fade" id="cetakKelasModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-print me-2"></i>Cetak Jadwal Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <small>Pilih Kelas untuk mencetak jadwal spesifik.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select id="printKelasId" class="form-select">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($allKelasList as $kelas)
                                <option value="{{ $kelas->id }}">
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitCetakKelas('pdf')">
                        <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitCetakKelas('excel')">
                        <i class="fas fa-file-excel me-1"></i> Cetak Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitCetakKelas(type) {
            const kelasId = document.getElementById('printKelasId').value;
            if (!kelasId) {
                showToast('warning', 'Silakan pilih kelas terlebih dahulu!');
                return;
            }

            let url = "";
            const tahunAjaranParam = "?tahun_ajaran_id={{ request('tahun_ajaran_id', $currentTahunAjaran->id) }}";

            if (type === 'excel') {
                // Endpoint Export Excel: /waka/jadwal-pelajaran/kelas/{id}/export-excel
                url = "{{ url('waka/jadwal-pelajaran/kelas') }}/" + kelasId + "/export-excel" + tahunAjaranParam;
            } else {
                // Endpoint Export PDF: /waka/jadwal-pelajaran/kelas/{id}/print
                url = "{{ url('waka/jadwal-pelajaran/kelas') }}/" + kelasId + "/print" + tahunAjaranParam;
            }
            
            window.open(url, '_blank');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cetakKelasModal'));
            modal.hide();
        }
    </script>
@endsection