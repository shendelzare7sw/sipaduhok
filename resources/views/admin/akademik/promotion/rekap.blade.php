@extends('layouts.sneat')

@section('title', 'Rekap Kenaikan Kelas')
@section('page-title', 'Rekap Kenaikan Kelas')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('styles')
@vite(['resources/css/admin/akademik/promotion/rekap.css'])
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y promotion-report">
    <div id="promotionRekapConfig" data-total-active-global="{{ $totalActiveGlobal ?? 0 }}"></div>
    <!-- Header removed, using layout title -->

    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-arrow-up"></i></div>
                <span>Naik Kelas</span>
                <strong>{{ $stats['NAIK_KELAS'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col">
            <div class="stat-card">
                <div class="stat-icon info"><i class="fas fa-graduation-cap"></i></div>
                <span>Lulus</span>
                <strong>{{ $stats['LULUS'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col">
            <div class="stat-card">
                <div class="stat-icon warning"><i class="fas fa-hand-holding-usd"></i></div>
                <span>Naik Dispensasi</span>
                <strong>{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col">
            <div class="stat-card">
                <div class="stat-icon primary"><i class="fas fa-user-check"></i></div>
                <span>Lulus Dispensasi</span>
                <strong>{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</strong>
                <small>Dengan izin tunggakan</small>
            </div>
        </div>
        <div class="col">
            <div class="stat-card">
                <div class="stat-icon danger"><i class="fas fa-times-circle"></i></div>
                <span>Tidak Naik</span>
                <strong>{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</strong>
            </div>
        </div>
    </div>

    @php
        $activeTab = in_array(request('tab'), ['history', 'scheduling']) ? request('tab') : 'simulation';
    @endphp

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3" id="promotionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'simulation' ? 'active' : '' }}" id="simulation-tab" data-bs-toggle="tab" data-bs-target="#simulation" type="button" role="tab" aria-controls="simulation" aria-selected="{{ $activeTab == 'simulation' ? 'true' : 'false' }}">
                <i class="fas fa-flask me-1"></i> <span class="d-none d-md-inline">Simulasi / Keadaan Sekarang</span><span class="d-inline d-md-none">Simulasi</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'history' ? 'active' : '' }}" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="{{ $activeTab == 'history' ? 'true' : 'false' }}">
                <i class="fas fa-history me-1"></i> <span class="d-none d-md-inline">Riwayat Eksekusi ({{ $students->total() }})</span><span class="d-inline d-md-none">Riwayat ({{ $students->total() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'scheduling' ? 'active' : '' }}" id="scheduling-tab" data-bs-toggle="tab" data-bs-target="#scheduling" type="button" role="tab" aria-controls="scheduling" aria-selected="{{ $activeTab == 'scheduling' ? 'true' : 'false' }}">
                <i class="fas fa-clock me-1"></i> <span class="d-none d-md-inline">Jadwal Otomatis</span><span class="d-inline d-md-none">Jadwal</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="promotionTabsContent">
        
        <!-- Tab 1: Riwayat Eksekusi (Existing) -->
        <div class="tab-pane fade {{ $activeTab == 'history' ? 'show active' : '' }}" id="history" role="tabpanel" aria-labelledby="history-tab">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        {{-- Title --}}
                        <div>
                            <h5 class="mb-0">Hasil Eksekusi Kenaikan Kelas</h5>
                            <small class="text-muted">Tahun Ajaran: {{ $tahun->nama_tahun_ajaran }}</small>
                        </div>

                        {{-- Actions: Print + Filter & Search (grouped so they stay together on the right) --}}
                        <div class="d-flex flex-wrap align-items-center gap-2 history-header-actions">
                            {{-- Print Button --}}
                            @php
                                $printRouteName = str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.kenaikan-kelas.report.print' : 'waka.kenaikan-kelas.report.print';
                            @endphp
                            <a href="{{ route($printRouteName, array_merge(request()->only(['tahun_ajaran_id', 'status', 'cabang_id', 'jenjang', 'kelas_id']))) }}"
                               target="_blank"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print me-1"></i> Cetak Laporan
                            </a>

                            {{-- Filter & Search --}}
                            <form action="{{ route(Route::currentRouteName()) }}" method="GET" id="historyFilterForm" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="tab" value="history">

                                {{-- Tahun Ajaran Selector (Priority) --}}
                                <select name="tahun_ajaran_id" class="form-select form-select-sm year-select-sm" data-auto-submit>
                                    @foreach($allTahunAjaran as $ta)
                                        <option value="{{ $ta->id }}" {{ $tahun->id == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->nama_tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Filter Dropdown --}}
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <i class="fas fa-filter me-1"></i> Filter
                                        @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId || $filterStatus)
                                            <span class="badge bg-primary ms-1">{{ collect([auth()->user()->role !== 'wakil_kepala_sekolah' ? $cabangId : null, $jenjangFilter, $kelasId, $filterStatus])->filter()->count() }}</span>
                                        @endif
                                    </button>
                                    <div class="dropdown-menu p-3 shadow filter-dropdown-menu">
                                        <h6 class="dropdown-header px-0 text-uppercase small fw-bold mb-2">Opsi Filter</h6>

                                        {{-- Filter Cabang (hanya untuk admin) --}}
                                        @if(auth()->user()->role !== 'wakil_kepala_sekolah')
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold mb-1">Cabang</label>
                                            <select name="cabang_id" class="form-select form-select-sm">
                                                <option value="">Semua Cabang</option>
                                                @foreach($cabangs as $cabang)
                                                    <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>
                                                        {{ $cabang->nama_cabang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif

                                        {{-- Filter Jenjang --}}
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold mb-1">Jenjang</label>
                                            <select name="jenjang" class="form-select form-select-sm">
                                                <option value="">Semua Jenjang</option>
                                                @foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $j)
                                                    <option value="{{ $j }}" {{ $jenjangFilter == $j ? 'selected' : '' }}>{{ $j }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Filter Kelas --}}
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold mb-1">Kelas</label>
                                            <select name="kelas_id" class="form-select form-select-sm">
                                                <option value="">Semua Kelas</option>
                                                @foreach($kelasList as $kelas)
                                                    <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                                        {{ $kelas->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Filter Status --}}
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold mb-1">Status Kelulusan</label>
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="">Semua Status</option>
                                                <option value="NAIK_KELAS" {{ $filterStatus == 'NAIK_KELAS' ? 'selected' : '' }}>Naik Kelas</option>
                                                <option value="LULUS" {{ $filterStatus == 'LULUS' ? 'selected' : '' }}>Lulus</option>
                                                <option value="NAIK_KELAS_TUNGGAKAN" {{ $filterStatus == 'NAIK_KELAS_TUNGGAKAN' ? 'selected' : '' }}>Naik (Dispensasi)</option>
                                                <option value="LULUS_TUNGGAKAN" {{ $filterStatus == 'LULUS_TUNGGAKAN' ? 'selected' : '' }}>Lulus (Dispensasi)</option>
                                                <option value="TIDAK_NAIK_KELAS" {{ $filterStatus == 'TIDAK_NAIK_KELAS' ? 'selected' : '' }}>Tidak Naik</option>
                                            </select>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-check me-1"></i> Terapkan Filter
                                            </button>
                                            @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId || $filterStatus)
                                                <a href="{{ route(Route::currentRouteName(), ['tab' => 'history', 'tahun_ajaran_id' => $tahun->id]) }}"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times me-1"></i> Reset Filter
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Search --}}
                                <div class="input-group search-input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                           placeholder="Cari nama..." value="{{ $search }}" autocomplete="off">
                                    @if($search)
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                data-clear-search
                                                title="Hapus pencarian">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-card-mobile">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Kelas Asal</th>
                                <th>Kelas Tujuan</th>
                                <th>Status Bayar</th>
                                <th>Akademik</th>
                                <th>Hasil Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $data)
                            @php
                                $badge = match($data->status_kelulusan) {
                                    'NAIK_KELAS' => 'success',
                                    'LULUS' => 'info',
                                    'LULUS_TUNGGAKAN' => 'primary',
                                    'NAIK_KELAS_TUNGGAKAN' => 'warning',
                                    'TIDAK_NAIK_KELAS' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <td class="mobile-card-head">
                                    <div class="d-flex justify-content-between align-items-start gap-2 student-summary-row">
                                        <div class="d-flex align-items-center gap-2 student-summary-main">
                                            <span class="text-wrap text-break lh-sm">{{ $data->nama_lengkap }}</span>
                                        </div>
                                        <div class="mobile-only-cell flex-shrink-0 ms-auto">
                                            <span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $data->status_kelulusan) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Kelas Asal">
                                    <div class="mobile-text-end">{{ $data->kelas_asal }}</div>
                                </td>
                                <td data-label="Kelas Tujuan">
                                    <div class="mobile-text-end">{{ $data->kelas_tujuan ?? '-' }}</div>
                                </td>
                                <td data-label="Status Bayar">
                                    <div class="mobile-text-end">
                                        @if($data->status_pembayaran == 'LUNAS')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-danger mb-1">Belum Lunas</span>
                                            @if($data->izin_khusus_ketua)
                                                <br><span class="badge bg-warning" title="Dispensasi Ketua">Override</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Akademik">
                                    <div class="mobile-text-end">
                                        @if($data->akademik_override_tanpa_jadwal ?? false)
                                            <span class="badge bg-warning text-dark" title="Kelas belum ada Jadwal Pelajaran saat dinaikkan - dilewatkan manual oleh admin, bukan hasil ukur nilai asli.">
                                                Aman <i class="fas fa-user-check ms-1"></i>
                                            </span>
                                            <br>
                                            <small class="text-muted">Override manual (belum ada jadwal)</small>
                                        @else
                                            {{ $data->persentase_nilai_tuntas }}% Tuntas
                                            <br>
                                            <small class="text-muted">{{ $data->jumlah_mapel_tuntas }}/{{ $data->total_mapel }} Mapel</small>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Hasil Akhir" class="desktop-only-cell">
                                    <span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $data->status_kelulusan) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">
                                            Belum ada data eksekusi kenaikan kelas. <br>
                                            Silakan klik tombol <strong>"Proses Kenaikan Kelas"</strong> di tab Simulasi untuk menjalankan sistem.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $students->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <!-- Tab 2: Simulasi (New) -->
        <div class="tab-pane fade {{ $activeTab == 'simulation' ? 'show active' : '' }}" id="simulation" role="tabpanel" aria-labelledby="simulation-tab">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        {{-- Title --}}
                        <div>
                            <h5 class="mb-0">Simulasi Kenaikan Kelas</h5>
                            <small class="text-muted">Tahun Ajaran: {{ $tahun->nama_tahun_ajaran }}</small>
                        </div>

                        <!-- Execution Button (Trigger) -->
                        @if($promotionReadiness['isReady'])
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#executeModal">
                                <i class="fas fa-cogs me-1"></i> Proses Kenaikan Kelas
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Siapkan Tahun Ajaran dan Kelas baru terlebih dahulu">
                                <i class="fas fa-lock me-1"></i> Proses Kenaikan Kelas
                            </button>
                        @endif
                    </div>

                    <!-- Simulation Mode Toggle -->
                    <div class="btn-group mb-3" role="group" aria-label="Simulation Mode">
                        <a href="{{ route(Route::currentRouteName(), array_merge(request()->except('sim_mode'), ['sim_mode' => 'current', 'tab' => 'simulation'])) }}"
                           class="btn btn-sm btn-{{ ($simMode ?? 'current') == 'current' ? 'primary' : 'outline-primary' }}">
                            <i class="fas fa-users me-1"></i> Keadaan Saat Ini
                        </a>
                        <a href="{{ route(Route::currentRouteName(), array_merge(request()->except('sim_mode'), ['sim_mode' => 'historical', 'tab' => 'simulation'])) }}"
                           class="btn btn-sm btn-{{ ($simMode ?? 'current') == 'historical' ? 'primary' : 'outline-primary' }}">
                            <i class="fas fa-history me-1"></i> Keadaan Saat Eksekusi
                        </a>
                    </div>

                    {{-- Filter & Search --}}
                    <form action="{{ route(Route::currentRouteName()) }}" method="GET" id="simulationFilterForm" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="tab" value="simulation">
                        <input type="hidden" name="sim_mode" value="{{ $simMode ?? 'current' }}">

                        {{-- Tahun Ajaran Selector (Priority) --}}
                        <select name="tahun_ajaran_id" class="form-select form-select-sm year-select-sm" data-auto-submit>
                            @foreach($allTahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ $tahun->id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filter Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Filter
                                @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId)
                                    <span class="badge bg-primary ms-1">{{ collect([auth()->user()->role !== 'wakil_kepala_sekolah' ? $cabangId : null, $jenjangFilter, $kelasId])->filter()->count() }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu p-3 shadow filter-dropdown-menu">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold mb-2">Opsi Filter</h6>

                                {{-- Filter Cabang (hanya untuk admin) --}}
                                @if(auth()->user()->role !== 'wakil_kepala_sekolah')
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Cabang</label>
                                    <select name="cabang_id" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangs as $cabang)
                                            <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                {{-- Filter Jenjang --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Jenjang</label>
                                    <select name="jenjang" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang</option>
                                        @foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $j)
                                            <option value="{{ $j }}" {{ $jenjangFilter == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Kelas --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold mb-1">Kelas</label>
                                    <select name="kelas_id" class="form-select form-select-sm">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-check me-1"></i> Terapkan Filter
                                    </button>
                                    @if((auth()->user()->role !== 'wakil_kepala_sekolah' && $cabangId) || $jenjangFilter || $kelasId)
                                        <a href="{{ route(Route::currentRouteName(), ['tab' => 'simulation', 'sim_mode' => $simMode ?? 'current', 'tahun_ajaran_id' => $tahun->id]) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i> Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="input-group search-input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Cari nama..." value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-clear-search
                                        title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    {{-- TA Readiness Warnings --}}
                    @if(!$promotionReadiness['hasNextTA'])
                        <div class="alert alert-danger d-flex align-items-start mb-3">
                            <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Tahun Ajaran Baru Belum Dibuat!</strong><br>
                                <small>Buat Tahun Ajaran baru terlebih dahulu sebelum menjalankan proses kenaikan kelas. 
                                <a href="{{ route('admin.tahun-ajaran.create') }}" class="alert-link">Buat Tahun Ajaran →</a></small>
                            </div>
                        </div>
                    @elseif($promotionReadiness['kelasBaruCount'] == 0)
                        <div class="alert alert-warning d-flex align-items-start mb-3">
                            <i class="fas fa-exclamation-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Kelas Belum Dibuat di TA Baru!</strong><br>
                                <small>Buat struktur kelas untuk TA {{ $promotionReadiness['nextTA']->nama_tahun_ajaran }} agar siswa bisa dipindahkan.
                                <a href="{{ route('admin.kelas.index') }}" class="alert-link">Kelola Kelas →</a></small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success d-flex align-items-start mb-3">
                            <i class="fas fa-check-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <strong>Siap untuk Kenaikan Kelas!</strong><br>
                                <small>TA Baru: <strong>{{ $promotionReadiness['nextTA']->nama_tahun_ajaran }}</strong> dengan {{ $promotionReadiness['kelasBaruCount'] }} kelas tersedia.</small>
                            </div>
                        </div>
                    @endif

                    @if(($simMode ?? 'current') === 'current')
                        @if($showTanpaJadwal ?? false)
                            <div class="alert alert-warning">
                                <i class="fas fa-user-clock me-1"></i>
                                Anda sedang melihat <strong>siswa yang kelasnya belum punya Jadwal Pelajaran</strong> -
                                status akademik mereka jujur tampil "Rawan 0%" (memang belum ada yang bisa diukur).
                                Kalau dinaikkan lewat <strong>"Naikkan Terpilih"</strong> di bawah, status akademiknya akan
                                otomatis ditandai <strong>"Aman"</strong> (override manual, bukan hasil ukur nilai asli) -
                                data akan terukur normal begitu jadwal TA berikutnya disetel.
                                <a href="{{ route(Route::currentRouteName(), array_merge(request()->except('tanpa_jadwal'), ['tab' => 'simulation'])) }}" class="alert-link">Kembali ke tampilan normal</a>.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i> Data di bawah ini adalah <strong>SIMULASI REAL-TIME</strong> berdasarkan data keuangan dan nilai saat ini.
                            </div>
                            @if(($siswaTanpaJadwalCount ?? 0) > 0)
                                @php
                                    $isAdminCtx = str_contains(Route::currentRouteName(), 'admin.');
                                    $jadwalRoute = $isAdminCtx ? 'admin.jadwal-pelajaran.index' : 'waka.jadwal-pelajaran.index';
                                    $siswaRoute = $isAdminCtx ? 'admin.users.siswa' : 'waka.manajemen-siswa.index';
                                @endphp
                                <div class="alert alert-warning py-2 mb-3">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    <strong>{{ $siswaTanpaJadwalCount }} siswa aktif disembunyikan</strong> dari simulasi ini karena kelasnya
                                    belum punya Jadwal Pelajaran sama sekali (belum bisa dinilai oleh guru manapun, bukan soal nilainya kurang).
                                    Setup jadwalnya dulu di <a href="{{ route($jadwalRoute) }}" class="alert-link">Jadwal Pelajaran</a>,
                                    pindahkan siswanya ke kelas lain lewat <a href="{{ route($siswaRoute) }}" class="alert-link">Kelola Siswa</a>,
                                    atau <a href="{{ route(Route::currentRouteName(), array_merge(request()->except('tanpa_jadwal'), ['tanpa_jadwal' => 1, 'tab' => 'simulation'])) }}" class="alert-link">tampilkan &amp; naikkan manual</a>.
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-secondary py-2 mb-3">
                            <i class="fas fa-lock me-1"></i> Tampilan ini adalah <strong>snapshot riwayat</strong> (kondisi siswa saat eksekusi terakhir dijalankan) dan bersifat baca-saja. Untuk menaikkan siswa yang tertinggal, gunakan tab <strong>Keadaan Saat Ini</strong>.
                        </div>
                    @endif

                    {{-- Mobile Select All --}}
                    @if(($simMode ?? 'current') === 'current')
                    <div class="mobile-select-all mb-2 align-items-center gap-2 px-2">
                        <input type="checkbox" id="selectAllSimMobile" data-toggle-all-checkboxes data-target-class="simCheck">
                        <label for="selectAllSimMobile" class="form-label mb-0 small fw-bold">Pilih Semua</label>
                    </div>
                    @endif

                    {{-- Select All Across Pages Banner --}}
                    @if(($simMode ?? 'current') === 'current' && $activeStudentsLinks->lastPage() > 1)
                    <div id="selectAllBanner" class="alert alert-warning py-2 px-3 mb-2 d-none">
                        <i class="fas fa-info-circle me-1"></i>
                        <span id="bannerText">Semua <strong>{{ $activeStudentsLinks->count() }}</strong> siswa di halaman ini dipilih.</span>
                        <a href="#" id="selectAllPagesLink" class="fw-bold ms-1" data-select-all-pages>
                            Pilih semua <strong>{{ $totalActiveGlobal ?? $activeStudentsLinks->total() }}</strong> siswa di semua halaman
                        </a>
                        <a href="#" id="clearSelectAllLink" class="fw-bold ms-1 d-none" data-clear-select-all-pages>
                            Batalkan pilih semua halaman
                        </a>
                    </div>
                    @endif

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover table-card-mobile">
                            <thead>
                                <tr>
                                    @if(($simMode ?? 'current') === 'current')
                                    <th class="table-checkbox-col"><input type="checkbox" id="selectAllSim" data-toggle-all-checkboxes data-target-class="simCheck"></th>
                                    @endif
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status Keuangan</th>
                                    <th>Status Akademik</th>
                                    <th>Prediksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($simulationData as $sim)
                                <tr>
                                    @if(($simMode ?? 'current') === 'current')
                                    <td class="desktop-only-cell">
                                        @if(!$sim['result']['eligible'])
                                        <input type="checkbox" class="simCheck" value="{{ $sim['siswa']->id }}">
                                        @endif
                                    </td>
                                    @endif
                                    <td class="mobile-card-head">
                                        <div class="d-flex justify-content-between align-items-start gap-2 student-summary-row">
                                            <div class="d-flex align-items-center gap-2 student-summary-main">
                                                @if(($simMode ?? 'current') === 'current' && !$sim['result']['eligible'])
                                                <input type="checkbox" class="simCheck mobile-only-cell flex-shrink-0 mobile-sim-checkbox" value="{{ $sim['siswa']->id }}">
                                                @endif
                                                <span class="text-wrap text-break lh-sm student-name-text">{{ $sim['siswa']->nama_lengkap }}</span>
                                            </div>
                                            <div class="mobile-only-cell flex-shrink-0 ms-auto">
                                                @if($sim['result']['eligible'])
                                                    @if(preg_match('/(9|IX|12|XII)/', strtoupper($sim['siswa']->kelas->nama_kelas ?? '')))
                                                        <span class="text-info fw-bold"><i class="fas fa-graduation-cap"></i></span>
                                                    @else
                                                        <span class="text-success fw-bold"><i class="fas fa-check-circle"></i></span>
                                                    @endif
                                                @else
                                                    <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i></span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Kelas">
                                        <div class="mobile-text-end">{{ $sim['siswa']->kelas->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td data-label="Keuangan">
                                        <div class="mobile-text-end">
                                            @if($sim['result']['financial']['status'] == 'LUNAS')
                                                <span class="badge bg-success">Lunas</span>
                                            @else
                                                <span class="badge bg-danger mb-1 promotion-debt-badge">Tunggakan: Rp {{ number_format($sim['result']['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                                                @if($sim['result']['financial']['is_dispensasi'])
                                                    <br><span class="badge bg-warning">Dispensasi OK</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Akademik">
                                        <div class="mobile-text-end">
                                            @if($sim['result']['academic']['is_tuntas'])
                                                <span class="badge bg-success">Aman ({{ $sim['result']['academic']['percentage'] }}%)</span>
                                            @else
                                                <span class="badge bg-danger mb-1">Rawan ({{ $sim['result']['academic']['percentage'] }}%)</span>
                                                <br><small class="text-muted lh-1">Hanya {{ $sim['result']['academic']['tuntas_count'] }} mapel tuntas</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="desktop-only-cell">
                                        @if($sim['result']['eligible'])
                                            @if(preg_match('/(9|IX|12|XII)/', strtoupper($sim['siswa']->kelas->nama_kelas ?? '')))
                                                <span class="text-info fw-bold"><i class="fas fa-graduation-cap"></i> Siap Lulus</span>
                                            @else
                                                <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Siap Naik</span>
                                            @endif
                                        @else
                                            <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Tertunda</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Bulk Promote Selected Form (hanya untuk mode "Keadaan Saat Ini") --}}
                    @if(($simMode ?? 'current') === 'current')
                    @php
                        $routePrefix = str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik' : 'waka';
                    @endphp
                    <form id="promoteSelectedForm" action="{{ route($routePrefix . '.kenaikan-kelas.promote-selected') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                        <input type="hidden" name="select_all" id="selectAllFlag" value="0">
                        {{-- Retain current filters to be passed when select_all is true --}}
                        <input type="hidden" name="cabang_id" value="{{ $cabangId }}">
                        <input type="hidden" name="jenjang" value="{{ $jenjangFilter }}">
                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                        <input type="hidden" name="search" value="{{ $search }}">
                    </form>
                    @endif

                    <div class="p-3 d-flex justify-content-between align-items-center sim-footer-area">
                        <div>
                            @if(($simMode ?? 'current') === 'current')
                            <button type="button" id="promoteSelectedTrigger" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#promoteSelectedModal" disabled>
                                <i class="fas fa-arrow-up me-1"></i> Naikkan Terpilih
                            </button>
                            @endif
                        </div>
                        {{ $activeStudentsLinks->appends(['tab' => 'simulation'])->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Scheduling (New) -->
        <div class="tab-pane fade {{ $activeTab == 'scheduling' ? 'show active' : '' }}" id="scheduling" role="tabpanel" aria-labelledby="scheduling-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Penjadwalan Eksekusi Kenaikan Kelas</h5>
                    {{-- Button Removed: Schedule is now managed in Settings --}}
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        Jadwal eksekusi otomatis diatur melalui menu 
                        <a href="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.kenaikan-kelas.settings.index' : 'waka.kenaikan-kelas.settings.index') }}" class="fw-bold">Pengaturan Kenaikan Kelas</a>.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-card-mobile">
                            <thead>
                                <tr>
                                    <th>Jadwal Eksekusi</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Statistik</th>
                                    <th>Log</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                <tr>
                                    <td class="mobile-card-head">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-calendar-alt me-1"></i> {{ $schedule->scheduled_at->format('d/m/y H:i') }}</span>
                                            @if($schedule->status == 'PENDING')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($schedule->status == 'RUNNING')
                                                <span class="badge bg-info">Sedang Berjalan</span>
                                            @elseif($schedule->status == 'COMPLETED')
                                                <span class="badge bg-success">Selesai</span>
                                            @elseif($schedule->status == 'FAILED')
                                                <span class="badge bg-danger">Gagal</span>
                                            @else
                                                <span class="badge bg-secondary">Batal</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="desktop-only-cell">
                                        @if($schedule->status == 'PENDING')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($schedule->status == 'RUNNING')
                                            <span class="badge bg-info">Sedang Berjalan</span>
                                        @elseif($schedule->status == 'COMPLETED')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($schedule->status == 'FAILED')
                                            <span class="badge bg-danger">Gagal</span>
                                        @else
                                            <span class="badge bg-secondary">Batal</span>
                                        @endif
                                    </td>
                                    <td data-label="Dibuat Oleh">
                                        <div class="mobile-text-end">
                                            <div>{{ $schedule->creator->name ?? '-' }}</div>
                                            @if($schedule->creator)
                                                <small class="text-muted">
                                                    {{ $schedule->creator->role === 'wakil_kepala_sekolah' ? 'Wakil Kepala Sekolah' : ucfirst($schedule->creator->role) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Statistik">
                                        <div class="mobile-text-end">
                                            @if($schedule->status == 'COMPLETED')
                                                <small>
                                                    Proses: {{ $schedule->students_processed }}<br>
                                                    Naik: {{ $schedule->students_promoted }}<br>
                                                    Lulus: {{ $schedule->students_graduated }}<br>
                                                    Gagal: {{ $schedule->students_failed }}
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Log">
                                        <div class="mobile-text-end">
                                            <small class="text-muted">{{ Str::limit($schedule->execution_log, 50) }}</small>
                                        </div>
                                    </td>
                                    <td class="mobile-card-actions">
                                        @if($schedule->status == 'PENDING')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cancelScheduleModal"
                                                data-url="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.kenaikan-kelas.cancel-schedule' : 'waka.kenaikan-kelas.cancel-schedule', $schedule->id) }}"
                                                data-date="{{ $schedule->scheduled_at->format('d/m/y H:i') }}">
                                                Batal
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada jadwal eksekusi otomatis.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Execution Confirmation (Existing) -->
    <div class="modal fade" id="executeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">Konfirmasi Kenaikan Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Apakah Anda Yakin?</h4>
                    </div>
                    
                    {{-- Checklist Status --}}
                    <div class="mb-3 debt-summary-box">
                        <strong class="d-block mb-2"><i class="fas fa-clipboard-check me-1"></i> Status Persiapan:</strong>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Tahun Ajaran Aktif: <strong>{{ $activeYear->nama_tahun_ajaran }}</strong></span>
                        </div>
                        @if($promotionReadiness['isReady'])
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Target TA Baru: <strong>{{ $promotionReadiness['nextTA']->nama_tahun_ajaran }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Kelas Tersedia: <strong>{{ $promotionReadiness['kelasBaruCount'] }} kelas</strong></span>
                        </div>
                        @endif
                    </div>
                    
                    <p class="mb-2">Proses ini akan:</p>
                    <ul class="text-start">
                        <li>Mengecek kelayakan <strong>SEMUA</strong> siswa aktif secara otomatis</li>
                        <li>Memindahkan siswa yang memenuhi syarat ke kelas baru di TA {{ $promotionReadiness['nextTA']->nama_tahun_ajaran ?? 'baru' }}</li>
                        <li>Menetapkan status <strong>Lulus</strong> untuk tingkat akhir</li>
                        <li>Siswa yang tidak memenuhi syarat akan tercatat sebagai "Tidak Naik Kelas"</li>
                    </ul>
                    
                    <div class="alert alert-warning py-2 mb-0">
                        <small><i class="fas fa-info-circle me-1"></i> Siswa yang tidak naik kelas bisa dinaikkan secara individual setelah syarat terpenuhi.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route(str_replace('.report', '.execute', Route::currentRouteName())) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-play me-1"></i> Ya, Proses Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cancel Schedule (New) -->
    <div class="modal fade" id="cancelScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pembatalan Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-times fa-3x text-danger mb-3"></i>
                        <h4>Batalkan Eksekusi?</h4>
                    </div>
                    <p>Apakah Anda yakin ingin membatalkan jadwal eksekusi otomatis pada:</p>
                    <h5 class="text-center text-primary" id="scheduleDateText"></h5>
                    <div class="alert alert-info mt-3">
                        <small><i class="fas fa-info-circle"></i> Tindakan ini juga akan mereset pengaturan tanggal eksekusi di menu Pengaturan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <form id="cancelScheduleForm" action="" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Ya, Batalkan Jadwal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Promote Selected Modal -->
    <div class="modal fade" id="promoteSelectedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="fas fa-arrow-up me-2"></i>Konfirmasi Kenaikan Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-graduate fa-3x text-success mb-3"></i>
                        <h4>Konfirmasi Proses Manual</h4>
                    </div>
                    
                    <p>Anda akan menaikkan <strong id="selectedCount" class="text-success fs-4">0</strong> siswa terpilih.</p>
                    <p id="selectAllWarning" class="text-warning fw-bold d-none mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Mode Pilih Semua Data diaktifkan.</p>
                    
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle me-1"></i> <strong>Konteks Tahun: {{ $tahun->nama_tahun_ajaran }}</strong><br>
                        Sistem akan mencari kelas lanjutan di tahun ajaran berikutnya secara otomatis.</small>
                    </div>

                    <p class="mb-0">Pastikan data siswa benar sebelum melanjutkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmPromoteBtn">
                        <i class="fas fa-check me-1"></i> Ya, Naikkan Siswa
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@vite(['resources/js/admin/akademik/promotion/rekap.js'])
@endsection
