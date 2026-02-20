@extends('layouts.sneat')

@section('page-title', 'Rekap Kenaikan Kelas')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header removed, using layout title -->

    <!-- Statistics - Reorganized into 2 rows -->
    <!-- Statistics - Single Row 5 Columns -->
    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">
        <div class="col">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5 class="card-title text-white">Naik Kelas</h5>
                    <h2>{{ $stats['NAIK_KELAS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5 class="card-title text-white">Lulus</h5>
                    <h2>{{ $stats['LULUS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h5 class="card-title text-dark">Naik (Dispensasi)</h5>
                    <h2>{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-label-primary h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Lulus (Dispensasi)</h5>
                    <h2 class="text-primary">{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</h2>
                    <small class="text-primary">Lulus meski ada tunggakan</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h5 class="card-title text-white">Tidak Naik</h5>
                    <h2>{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    @php
        $activeTab = request('tab') == 'history' ? 'history' : 'simulation';
    @endphp

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3" id="promotionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'simulation' ? 'active' : '' }}" id="simulation-tab" data-bs-toggle="tab" data-bs-target="#simulation" type="button" role="tab" aria-controls="simulation" aria-selected="{{ $activeTab == 'simulation' ? 'true' : 'false' }}">
                <i class="fas fa-flask me-1"></i> Simulasi / Keadaan Sekarang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'history' ? 'active' : '' }}" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="{{ $activeTab == 'history' ? 'true' : 'false' }}">
                <i class="fas fa-history me-1"></i> Riwayat Eksekusi ({{ $students->total() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab == 'scheduling' ? 'active' : '' }}" id="scheduling-tab" data-bs-toggle="tab" data-bs-target="#scheduling" type="button" role="tab" aria-controls="scheduling" aria-selected="{{ $activeTab == 'scheduling' ? 'true' : 'false' }}">
                <i class="fas fa-clock me-1"></i> Jadwal Otomatis
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

                        {{-- Filter & Search --}}
                        <form action="{{ route(Route::currentRouteName()) }}" method="GET" id="historyFilterForm" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="tab" value="history">

                            {{-- Tahun Ajaran Selector (Priority) --}}
                            <select name="tahun_ajaran_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
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
                                    @if($cabangId || $kelasId || $filterStatus)
                                        <span class="badge bg-primary ms-1">{{ collect([$cabangId, $kelasId, $filterStatus])->filter()->count() }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                                    <h6 class="dropdown-header px-0 text-uppercase small fw-bold mb-2">Opsi Filter</h6>

                                    {{-- Filter Cabang --}}
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
                                        @if($cabangId || $kelasId || $filterStatus)
                                            <a href="{{ route(Route::currentRouteName(), ['tab' => 'history', 'tahun_ajaran_id' => $tahun->id]) }}"
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times me-1"></i> Reset Filter
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Search --}}
                            <div class="input-group" style="width: 220px;">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Cari nama..." value="{{ $search }}" autocomplete="off">
                                @if($search)
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="this.previousElementSibling.value=''; this.form.submit();"
                                            title="Hapus pencarian">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
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
                            <tr>
                                <td>{{ $data->nama_lengkap }}</td>
                                <td>{{ $data->kelas_asal }}</td>
                                <td>{{ $data->kelas_tujuan ?? '-' }}</td>
                                <td>
                                    @if($data->status_pembayaran == 'LUNAS')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                        @if($data->izin_khusus_ketua)
                                            <span class="badge bg-warning" title="Dispensasi Ketua">Override</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $data->persentase_nilai_tuntas }}% Tuntas
                                    <br>
                                    <small class="text-muted">{{ $data->jumlah_mapel_tuntas }}/{{ $data->total_mapel }} Mapel</small>
                                </td>
                                <td>
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
                    <div class="p-3">
                        {{ $students->withQueryString()->links() }}
                    </div>
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
                        <select name="tahun_ajaran_id" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
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
                                @if($cabangId || $kelasId)
                                    <span class="badge bg-primary ms-1">{{ collect([$cabangId, $kelasId])->filter()->count() }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu p-3 shadow" style="min-width: 280px;">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold mb-2">Opsi Filter</h6>

                                {{-- Filter Cabang --}}
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
                                    @if($cabangId || $kelasId)
                                        <a href="{{ route(Route::currentRouteName(), ['tab' => 'simulation', 'sim_mode' => $simMode ?? 'current', 'tahun_ajaran_id' => $tahun->id]) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-times me-1"></i> Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Search --}}
                        <div class="input-group" style="width: 220px;">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-sm"
                                   placeholder="Cari nama..." value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="this.previousElementSibling.value=''; this.form.submit();"
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

                     <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Data di bawah ini adalah <strong>SIMULASI REAL-TIME</strong> berdasarkan data keuangan dan nilai saat ini.
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"><input type="checkbox" id="selectAllSim" onclick="toggleAllCheckboxes(this, 'simCheck')"></th>
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
                                    <td>
                                        @if(!$sim['result']['eligible'])
                                        <input type="checkbox" class="simCheck" name="siswa_ids[]" value="{{ $sim['siswa']->id }}" form="promoteSelectedForm">
                                        @endif
                                    </td>
                                    <td>{{ $sim['siswa']->nama_lengkap }}</td>
                                    <td>{{ $sim['siswa']->kelas->nama_kelas ?? '-' }}</td>
                                    <td>
                                        @if($sim['result']['financial']['status'] == 'LUNAS')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-danger">Tunggakan: Rp {{ number_format($sim['result']['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                                            @if($sim['result']['financial']['is_dispensasi'])
                                                <span class="badge bg-warning">Dispensasi OK</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($sim['result']['academic']['is_tuntas'])
                                            <span class="badge bg-success">Aman ({{ $sim['result']['academic']['percentage'] }}%)</span>
                                        @else
                                            <span class="badge bg-danger">Rawan ({{ $sim['result']['academic']['percentage'] }}%)</span>
                                            <br><small>Hanya {{ $sim['result']['academic']['tuntas_count'] }} mapel tuntas</small>
                                        @endif
                                    </td>
                                    <td>
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
                        
                        {{-- Bulk Promote Selected Form --}}
                        @php
                            $routePrefix = str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik' : 'waka';
                        @endphp
                        <form id="promoteSelectedForm" action="{{ route($routePrefix . '.promotion.promote-selected') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                        </form>
                        
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" id="promoteSelectedTrigger" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#promoteSelectedModal" disabled>
                                    <i class="fas fa-arrow-up me-1"></i> Naikkan Terpilih
                                </button>
                            </div>
                           {{ $activeStudentsLinks->appends(['tab' => 'simulation'])->withQueryString()->links() }}
                        </div>
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
                        <a href="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.settings.index' : 'waka.promotion.settings.index') }}" class="fw-bold">Pengaturan Kenaikan Kelas</a>.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                                    <td>{{ $schedule->scheduled_at->format('d/m/y H:i') }}</td>
                                    <td>
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
                                    <td>
                                        <div>{{ $schedule->creator->name ?? '-' }}</div>
                                        @if($schedule->creator)
                                            <small class="text-muted">
                                                {{ $schedule->creator->role === 'wakil_kepala_sekolah' ? 'Wakil Kepala Sekolah' : ucfirst($schedule->creator->role) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                    <td><small class="text-muted">{{ Str::limit($schedule->execution_log, 50) }}</small></td>
                                    <td>
                                        @if($schedule->status == 'PENDING')
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelScheduleModal"
                                                data-url="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.cancel-schedule' : 'waka.promotion.cancel-schedule', $schedule->id) }}"
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
                    <div class="mb-3" style="background: #f8f9fa; border-radius: 8px; padding: 12px;">
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
                    
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle me-1"></i> <strong>Konteks Tahun: {{ $tahun->nama_tahun_ajaran }}</strong><br>
                        Sistem akan mencari kelas lanjutan di tahun ajaran berikutnya secara otomatis.</small>
                    </div>

                    <p class="mb-0">Pastikan data siswa benar sebelum melanjutkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('promoteSelectedForm').submit()">
                        <i class="fas fa-check me-1"></i> Ya, Naikkan Siswa
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function toggleAllCheckboxes(source, className) {
    const checkboxes = document.querySelectorAll('.' + className);
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });
}

// Update Modal Count
document.addEventListener('DOMContentLoaded', function() {
    var promoteBtn = document.querySelector('[data-bs-target="#promoteSelectedModal"]');
    if (promoteBtn) {
        promoteBtn.addEventListener('click', function() {
            var checkedBoxes = document.querySelectorAll('.simCheck:checked');
            document.getElementById('selectedCount').textContent = checkedBoxes.length;
            
            if (checkedBoxes.length === 0) {
                alert('Pilih siswa terlebih dahulu!');
                // Prevent modal show? Bootstrap handles click first, so maybe simple alert is cleaner, 
                // but let's rely on backend or just disable button if 0?
                // Better UX: Disable button if 0 checked.
            }
        });
    }
    
    // Optional: Real-time disable/enable button
    var checkboxes = document.querySelectorAll('.simCheck');
    var triggerBtn = document.querySelector('#promoteSelectedTrigger');
    
    function updateButtonState() {
        var count = document.querySelectorAll('.simCheck:checked').length;
        if(triggerBtn) triggerBtn.disabled = count === 0;
    }
    
    checkboxes.forEach(cb => cb.addEventListener('change', updateButtonState));
    // Initial State
    updateButtonState();

    // Existing Cancel Modal Script
    var cancelModal = document.getElementById('cancelScheduleModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var url = button.getAttribute('data-url');
            var date = button.getAttribute('data-date');
            
            var form = cancelModal.querySelector('#cancelScheduleForm');
            var dateText = cancelModal.querySelector('#scheduleDateText');
            
            form.action = url;
            dateText.textContent = date;
        });
    }
});
</script>
@endsection
