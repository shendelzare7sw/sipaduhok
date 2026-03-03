@extends('layouts.sneat')

@section('title', 'Presensi Siswa')
@section('page-title', 'Presensi Siswa')
@section('page-subtitle', isset($kelas) && $kelas ? 'Kelola presensi siswa kelas ' . $kelas->nama_kelas : 'Kelola presensi siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .status-select { font-weight: 700; border-radius: 5px; }
    .rekap-cell { font-weight: 700; text-align: center; }
    .table-rekap thead th { vertical-align: middle; text-align: center; font-size: 11px; text-transform: uppercase; }
    .bg-hadir { background-color: #f6fff9 !important; }
    .bg-sakit { background-color: #fffdf0 !important; }
    .bg-izin  { background-color: #f0f7ff !important; }
    .bg-alpha { background-color: #fff5f5 !important; }
    select option.text-success { color: #1cc88a; }
    select option.text-warning { color: #f6c23e; }
    select option.text-primary { color: #4e73df; }
    select option.text-danger  { color: #e74a3b; }

    /* ── Responsive button grid for header actions ─────── */
    .presensi-btn-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* 2-col on mobile */
        gap: 8px;
        width: 100%;
    }
    @media (min-width: 576px) {
        .presensi-btn-grid {
            display: flex;           /* single row on sm+ */
            flex-wrap: nowrap;
            gap: 8px;
            width: auto;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">
    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning shadow-sm border-start border-warning border-4 alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><strong>Perhatian:</strong> {{ session('warning') }}
            @if(session('import_errors'))
                <ul class="mt-2 mb-0 small">
                    @foreach(session('import_errors') as $ie)
                        <li>{{ $ie }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HEADER ACTIONS --}}
    @if($kelas)
    <div class="card shadow mb-4 text-center text-sm-start">
        <div class="card-body py-3">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-2">
                <h5 class="m-0 fw-bold text-primary">Presensi Kelas {{ $kelas->nama_kelas }}</h5>
                {{-- Button grid: 2-col on mobile, single row on sm+ --}}
                <div class="presensi-btn-grid">
                    <a href="{{ route('wali.presensi.validasi-izin') }}" class="btn btn-warning btn-sm fw-bold text-white">
                        <i class="fas fa-check-circle me-1"></i> Validasi Izin
                    </a>
                    <button type="button" class="btn btn-success btn-sm fw-bold"
                        data-bs-toggle="modal" data-bs-target="#importPresensiModal">
                        <i class="fas fa-file-excel me-1"></i> Import Excel
                    </button>
                    <a href="{{ route('wali.presensi.print-rekap', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-secondary btn-sm fw-bold">
                        <i class="fas fa-print me-1"></i> Cetak Rekap
                    </a>
                    <a href="{{ route('wali.presensi.riwayat') }}" class="btn btn-info btn-sm fw-bold text-white">
                        <i class="fas fa-history me-1"></i> Riwayat & Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($kelas)
    {{-- FILTER TANGGAL --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Laporan & Tanggal</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('wali.presensi.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small fw-bold">TANGGAL PRESENSI</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small fw-bold">LIHAT BULAN</label>
                        <select name="bulan" class="form-select" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(now()->year, $m, 1)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small fw-bold">TAHUN</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('wali.presensi.index') }}" class="btn btn-light w-100 border fw-bold text-primary">
                            <i class="fas fa-sync-alt me-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- INPUT PRESENSI HARIAN --}}
    <div class="card shadow mb-4 border-start border-primary border-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-edit me-2"></i>Input: {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <form id="formPresensi" action="{{ route('wali.presensi.input-harian') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" width="50">NO</th>
                                <th>IDENTITAS SISWA</th>
                                <th width="200" class="text-center">STATUS KEHADIRAN</th>
                                <th>KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $index => $siswa)
                                @php
                                    $presensi = $presensiData[$siswa->id] ?? null;
                                    $status = $presensi ? $presensi->status : 'hadir';
                                @endphp
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-800">{{ $siswa->nama_lengkap }}</div>
                                        <div class="small text-muted">NIS: {{ $siswa->nis }}</div>
                                        <input type="hidden" name="presensi[{{ $index }}][siswa_id]" value="{{ $siswa->id }}">
                                    </td>
                                    <td class="align-middle">
                                        <select name="presensi[{{ $index }}][status]" class="form-select status-select text-center shadow-sm" required>
                                            <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }} class="text-success fw-bold">
                                                &#10004; Hadir
                                            </option>
                                            <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }} class="text-warning fw-bold">
                                                &#129308; Sakit
                                            </option>
                                            <option value="izin" {{ $status == 'izin' ? 'selected' : '' }} class="text-primary fw-bold">
                                                &#128221; Izin
                                            </option>
                                            <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }} class="text-danger fw-bold">
                                                &#10006; Alpha
                                            </option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="presensi[{{ $index }}][keterangan]" class="form-control form-control-sm bg-light"
                                               placeholder="Catatan..." value="{{ $presensi ? $presensi->keterangan : '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white text-end py-3">
                    <button type="button" class="btn btn-primary px-5 shadow fw-bold" data-bs-toggle="modal" data-bs-target="#konfirmasiSimpanModal">
                        <i class="fas fa-save me-2"></i>Simpan Data Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- REKAP BULANAN --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom-0">
            <h6 class="m-0 fw-bold text-gray-800">
                <i class="fas fa-book-open me-2 text-info"></i>Rekapitulasi: {{ \Carbon\Carbon::create($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}
            </h6>
        </div>
        <div class="card-body p-0 text-center">
            <div class="table-responsive">
                <table class="table table-bordered table-rekap mb-0">
                    <thead class="bg-gray-100">
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2" class="text-start">Nama Siswa</th>
                            <th colspan="4" class="py-2">Ringkasan Status</th>
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr>
                            <th class="text-success bg-hadir py-1"><i class="fas fa-check me-1"></i>H</th>
                            <th class="text-warning bg-sakit py-1"><i class="fas fa-thermometer-half me-1"></i>S</th>
                            <th class="text-primary bg-izin py-1"><i class="fas fa-envelope me-1"></i>I</th>
                            <th class="text-danger bg-alpha py-1"><i class="fas fa-times me-1"></i>A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaList as $index => $siswa)
                            @php
                                $rekap = $rekapBulan[$siswa->id];
                                $total = $rekap['hadir'] + $rekap['sakit'] + $rekap['izin'] + $rekap['alpha'];
                            @endphp
                            <tr>
                                <td class="align-middle">{{ $index + 1 }}</td>
                                <td class="text-start fw-bold align-middle text-gray-800">{{ $siswa->nama_lengkap }}</td>
                                <td class="rekap-cell bg-hadir text-success align-middle">{{ $rekap['hadir'] }}</td>
                                <td class="rekap-cell bg-sakit text-warning align-middle">{{ $rekap['sakit'] }}</td>
                                <td class="rekap-cell bg-izin text-primary align-middle">{{ $rekap['izin'] }}</td>
                                <td class="rekap-cell bg-alpha text-danger align-middle">{{ $rekap['alpha'] }}</td>
                                <td class="rekap-cell fw-bold bg-light align-middle text-dark">{{ $total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

{{-- MODAL KONFIRMASI SIMPAN --}}
<div class="modal fade" id="konfirmasiSimpanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-question-circle me-2"></i>Konfirmasi Simpan Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-save fa-3x text-primary mb-3"></i>
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menyimpan data presensi?</h6>
                <p class="text-muted small mb-0">Data yang sudah disimpan akan menggantikan data presensi sebelumnya untuk tanggal ini.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tidak
                </button>
                <button type="button" class="btn btn-primary" onclick="submitPresensi()">
                    <i class="fas fa-check me-1"></i> Ya, Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function submitPresensi() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiSimpanModal'));
        modal.hide();
        document.getElementById('formPresensi').submit();
    }

    // Sync tanggal filter → import modal tanggal field
    document.addEventListener('DOMContentLoaded', function () {
        const filterTanggal = document.querySelector('input[name="tanggal"]');
        const importTanggal = document.getElementById('importTanggal');
        if (filterTanggal && importTanggal) {
            importTanggal.value = filterTanggal.value;
            filterTanggal.addEventListener('change', function () {
                importTanggal.value = this.value;
            });
        }
    });
</script>
@endsection

{{-- MODAL IMPORT EXCEL --}}
<div class="modal fade" id="importPresensiModal" tabindex="-1" aria-labelledby="importPresensiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="importPresensiModalLabel">
                    <i class="fas fa-file-excel me-2"></i>Import Presensi dari Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('wali.presensi.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id ?? '' }}">
                <div class="modal-body">

                    {{-- Petunjuk --}}
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Petunjuk import:</strong>
                        <ol class="mt-1 mb-0 ps-3">
                            <li>Download template di bawah — template sudah berisi daftar nama & NIS siswa.</li>
                            <li>Isi kolom <strong>Status</strong> dengan: <code>hadir</code>, <code>sakit</code>, <code>izin</code>, atau <code>alpha</code>.</li>
                            <li>Kolom <strong>Keterangan</strong> bersifat opsional.</li>
                            <li><strong>Jangan ubah</strong> kolom NIS — digunakan untuk mencocokkan data.</li>
                            <li>Simpan file lalu upload di sini.</li>
                        </ol>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small">TANGGAL PRESENSI</label>
                        <input type="date" name="tanggal" id="importTanggal" class="form-control"
                               value="{{ $tanggal }}" required>
                    </div>

                    {{-- Download Template --}}
                    <div class="mb-3">
                        <a id="btnDownloadTemplate"
                           href="{{ route('wali.presensi.download-template', ['tanggal' => $tanggal]) }}"
                           class="btn btn-outline-success btn-sm w-100 fw-bold">
                            <i class="fas fa-download me-1"></i> Download Template Excel
                        </a>
                        <div class="text-muted small mt-1 text-center">
                            Template berisi daftar siswa kelas {{ $kelas->nama_kelas ?? '' }} siap diisi.
                        </div>
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-2">
                        <label class="form-label fw-bold small">UPLOAD FILE EXCEL / CSV</label>
                        <input type="file" name="file_excel" class="form-control"
                               accept=".csv,.xlsx,.xls" required>
                        @error('file_excel')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="text-muted small mt-1">Format: .csv, .xlsx, atau .xls (maks. 2 MB)</div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold">
                        <i class="fas fa-upload me-1"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>