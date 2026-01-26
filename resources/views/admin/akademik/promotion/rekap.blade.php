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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#executeModal">
                            <i class="fas fa-cogs me-1"></i> Proses Kenaikan Kelas
                        </button>
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
                     <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Data di bawah ini adalah <strong>SIMULASI REAL-TIME</strong> berdasarkan data keuangan dan nilai saat ini.
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
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
                        <div class="p-3">
                           {{ $activeStudentsLinks->appends(['tab' => 'simulation'])->withQueryString()->links() }}
                        </div>
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
                    <p class="mb-2">Proses ini akan melakukan hal berikut:</p>
                    <ul class="text-start">
                        <li>Mengecek kelayakan <strong>SEMUA</strong> siswa aktif secara otomatis.</li>
                        <li>Menaikkan kelas siswa yang memenuhi syarat.</li>
                        <li>Menetapkan status Lulus untuk tingkat akhir yang memenuhi syarat.</li>
                        <li><strong>Data perubahan bersifat permanen dan tidak bisa dibatalkan dengan mudah.</strong></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route(str_replace('.report', '.execute', Route::currentRouteName())) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Ya, Proses Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
