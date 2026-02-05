@extends('layouts.sneat')

@section('title', 'Rekapitulasi Kenaikan Kelas')

@section('sidebar-menu')
@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik /</span> Rekap Kenaikan Kelas</h4>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Naik Kelas</h5>
                    <h2>{{ $stats['NAIK_KELAS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">Lulus</h5>
                    <h2>{{ $stats['LULUS'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title text-dark">Naik (Dispensasi)</h5>
                    <h2>{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
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
                    <h5 class="mb-3">Hasil Eksekusi Kenaikan Kelas</h5>
                    
                    <form action="{{ route(Route::currentRouteName()) }}" method="GET" class="d-flex gap-2 flex-wrap">
                         <!-- Preserve Tab -->
                         <input type="hidden" name="tab" value="history">

                        <!-- Filter Tahun Ajaran -->
                        <select name="tahun_ajaran_id" class="form-select" style="width: 150px;" onchange="this.form.submit()">
                            @foreach($allTahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ $tahun->id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Search -->
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Siswa..." value="{{ $search }}">
                        </div>

                        <!-- Filter Cabang -->
                        <select name="cabang_id" class="form-select" style="width: 150px;">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>

                        <!-- Filter Kelas -->
                        <select name="kelas_id" class="form-select" style="width: 150px;">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Filter Status (Existing) -->
                        <select name="status" class="form-select" style="width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="NAIK_KELAS" {{ $filterStatus == 'NAIK_KELAS' ? 'selected' : '' }}>Naik Kelas</option>
                            <option value="LULUS" {{ $filterStatus == 'LULUS' ? 'selected' : '' }}>Lulus</option>
                            <option value="NAIK_KELAS_TUNGGAKAN" {{ $filterStatus == 'NAIK_KELAS_TUNGGAKAN' ? 'selected' : '' }}>Dispensasi</option>
                            <option value="TIDAK_NAIK_KELAS" {{ $filterStatus == 'TIDAK_NAIK_KELAS' ? 'selected' : '' }}>Tidak Naik</option>
                        </select>

                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if($search || $cabangId || $kelasId || $filterStatus)
                            <a href="{{ route(Route::currentRouteName()) }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </form>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Simulasi / Keadaan Siswa Sekarang</h5>
                        
                        <!-- Execution Button (Trigger) -->
                        @if($promotionReadiness['isReady'])
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#executeModal">
                                <i class="fas fa-cogs me-1"></i> Proses Kenaikan Kelas
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled title="Siapkan Tahun Ajaran dan Kelas baru terlebih dahulu">
                                <i class="fas fa-lock me-1"></i> Proses Kenaikan Kelas
                            </button>
                        @endif
                    </div>

                    <form action="{{ route(Route::currentRouteName()) }}" method="GET" class="d-flex gap-2 flex-wrap">
                         <!-- Preserve Tab -->
                         <input type="hidden" name="tab" value="simulation">

                        <!-- Search -->
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Siswa..." value="{{ $search }}">
                        </div>

                        <!-- Filter Cabang -->
                        <select name="cabang_id" class="form-select" style="width: 150px;">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>

                        <!-- Filter Kelas -->
                        <select name="kelas_id" class="form-select" style="width: 150px;">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if($search || $cabangId || $kelasId)
                            <a href="{{ route(Route::currentRouteName(), ['tab' => 'simulation']) }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
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
                        </form>
                        
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" form="promoteSelectedForm" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin menaikkan siswa yang dipilih?')">
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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="fas fa-plus me-1"></i> Buat Jadwal Baru
                    </button>
                </div>
                <div class="card-body">
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
                                    <td>{{ $schedule->scheduled_at->format('d M Y H:i') }}</td>
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
                                    <td>{{ $schedule->creator->nama_lengkap ?? '-' }}</td>
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
                                            <form action="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.cancel-schedule' : 'waka.promotion.cancel-schedule', $schedule->id) }}" method="POST" onsubmit="return confirm('Batalkan jadwal ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Batal</button>
                                            </form>
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

    <!-- Modal Execution Confirmation -->
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
                        <button type="submit" class="btn btn-danger"><i class="fas fa-play me-1"></i> Ya, Proses Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Modal Create Schedule -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route(str_contains(Route::currentRouteName(), 'admin.') ? 'admin.akademik.promotion.schedule' : 'waka.promotion.schedule') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Jadwalkan Eksekusi Otomatis</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> Eksekusi akan berjalan otomatis di latar belakang pada waktu yang ditentukan.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Eksekusi</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" required min="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Notifikasi (Opsional)</label>
                            <input type="email" name="notify_email" class="form-control" value="{{ auth()->user()->email }}" placeholder="Email untuk laporan hasil">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function toggleAllCheckboxes(source, className) {
    const checkboxes = document.querySelectorAll('.' + className);
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });
}
</script>
@endpush
