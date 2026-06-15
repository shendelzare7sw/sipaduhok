@extends('layouts.sneat')

@section('title', 'Validasi Akses Rapor')
@section('page-title', 'Validasi Akses Rapor - Ketua PKBM')
@section('page-subtitle', 'Tinjau dan setujui rapor yang dikirim wali kelas')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/ketua/validasi-rapor/index.css'])
@endsection

@section('content')
<div class="container-fluid">
    <div
        id="validasiRaporConfig"
        data-bulk-route="{{ route('ketua.validasi-rapor.bulk-validasi') }}"
        data-csrf="{{ csrf_token() }}"
    ></div>

    {{-- ALUR INFO --}}
    <div class="alert alert-light border border-primary border-opacity-25 shadow-sm mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <small class="text-muted fw-bold text-uppercase">Alur Review Rapor:</small>
            <span class="badge bg-info"><i class="fas fa-paper-plane me-1"></i>1. Wali Kirim Rapor</span>
            <i class="fas fa-arrow-right text-muted small"></i>
            <span class="badge bg-warning text-white"><i class="fas fa-eye me-1"></i>2. Ketua Preview & Review</span>
            <i class="fas fa-arrow-right text-muted small"></i>
            <span class="badge bg-success"><i class="fas fa-check me-1"></i>3. Validasi / Minta Revisi</span>
            <i class="fas fa-arrow-right text-muted small"></i>
            <span class="badge bg-primary"><i class="fas fa-money-bill me-1"></i>4. Lanjut ke Bendahara</span>
        </div>
        <div class="mt-2 small text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Klik <strong>Preview</strong> untuk melihat rapor sebelum menyetujui.
            Jika ada kesalahan, klik <strong>Revisi</strong> untuk mengembalikan rapor ke Wali Kelas dengan catatan perbaikan.
            Setelah validasi, rapor diteruskan ke Bendahara untuk verifikasi keuangan.
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Menunggu Validasi Ketua</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['pendingTotal'] }}</div>
                            <small class="text-muted">Rapor yang sudah dikirim wali kelas</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Divalidasi Hari Ini</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['validatedToday'] }}</div>
                            <small class="text-muted">Siswa yang divalidasi hari ini</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ketua.validasi-rapor.index') }}">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold">Cabang</label>
                        <select name="cabang_id" class="form-select" data-auto-submit>
                            <option value="">Semua Cabang</option>
                            @foreach($cabangList as $cabang)
                                <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold">Jenjang</label>
                        <select name="jenjang" class="form-select" data-auto-submit>
                            <option value="">Semua Jenjang</option>
                            @foreach($jenjangList as $jenjang)
                                <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>
                                    {{ $jenjang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold">Kelas</label>
                        <select name="kelas_id" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold">Status Validasi Ketua</label>
                        <select name="status_ketua" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_ketua') == 'pending' ? 'selected' : '' }}>Belum Divalidasi</option>
                            <option value="validated" {{ request('status_ketua') == 'validated' ? 'selected' : '' }}>Sudah Divalidasi</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small fw-bold">Cari Nama / NIS</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="ketua-filter-actions mt-3">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('ketua.validasi-rapor.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Daftar Siswa</h6>
            <div class="ketua-bulk-actions">
                <button type="button" class="btn btn-success btn-sm shadow-sm" id="btnValidasiSemua">
                    <i class="fas fa-check-double me-1"></i> Validasi Semua
                </button>
                <button type="button" class="btn btn-info btn-sm shadow-sm" id="btnValidasiTerpilih">
                    <i class="fas fa-check me-1"></i> Validasi Terpilih
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th width="50" class="text-center">No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th width="90" class="text-center">Wali Kelas</th>
                            <th width="110" class="text-center">Status Ketua</th>
                            <th width="180" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $siswa)
                        <tr>
                            <td class="text-center align-middle" data-label="Pilih">
                                @if(!$siswa->validasi_rapor_ketua)
                                    <input type="checkbox" class="siswa-checkbox form-check-input" value="{{ $siswa->id }}">
                                @endif
                            </td>
                            <td class="text-center align-middle fw-bold text-muted" data-label="No">{{ $loop->iteration }}</td>
                            <td class="align-middle fw-bold" data-label="NIS">{{ $siswa->nis }}</td>
                            <td class="align-middle" data-label="Nama">
                                <div class="fw-bold">{{ $siswa->nama_lengkap }}</div>
                            </td>
                            <td class="align-middle" data-label="Kelas">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td class="text-center align-middle" data-label="Wali Kelas">
                                @if($siswa->validasi_rapor_wali)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Sudah</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Belum</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" data-label="Status Ketua">
                                @if($siswa->validasi_rapor_ketua)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Valid</span>
                                    <div><small class="text-muted">{{ $siswa->tanggal_validasi_rapor_ketua ? $siswa->tanggal_validasi_rapor_ketua->format('d/m/Y') : '' }}</small></div>
                                @else
                                    <span class="badge bg-warning text-white"><i class="fas fa-clock me-1"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" data-label="Aksi">
                                <div class="ketua-row-actions">
                                    <a href="{{ route('ketua.validasi-rapor.preview', $siswa->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank" title="Preview Rapor">
                                        <i class="fas fa-eye me-1"></i> Preview
                                    </a>
                                    @if($siswa->validasi_rapor_ketua)
                                        <button type="button" class="btn btn-danger btn-sm shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#batalkanModal"
                                                data-action="{{ route('ketua.validasi-rapor.batalkan', $siswa->id) }}"
                                                data-name="{{ $siswa->nama_lengkap }}">
                                            <i class="fas fa-times me-1"></i> Batalkan
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#validasiModal"
                                                data-action="{{ route('ketua.validasi-rapor.validasi', $siswa->id) }}"
                                                data-name="{{ $siswa->nama_lengkap }}">
                                            <i class="fas fa-check me-1"></i> Validasi
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#revisiModal"
                                                data-action="{{ route('ketua.validasi-rapor.minta-revisi', $siswa->id) }}"
                                                data-name="{{ $siswa->nama_lengkap }}">
                                            <i class="fas fa-edit me-1"></i> Revisi
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block text-gray-300"></i>
                                Tidak ada data siswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $siswaList->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ======================== MODALS ======================== --}}

{{-- Modal: Validasi per siswa --}}
<div class="modal fade" id="validasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Validasi Rapor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h6 class="fw-bold mb-1">Validasi rapor untuk:</h6>
                <p class="text-primary fw-bold mb-3" id="validasiNamaSiswa">-</p>
                <div class="alert alert-success bg-light border-success text-start small mb-0">
                    <ul class="mb-0">
                        <li>Status validasi Ketua PKBM akan menjadi <strong>Disetujui</strong></li>
                        <li>Rapor akan diteruskan ke Bendahara untuk diproses</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="validasiForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Ya, Validasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Batalkan validasi per siswa --}}
<div class="modal fade" id="batalkanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-times-circle me-2"></i>Batalkan Validasi Rapor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-1">Batalkan validasi rapor untuk:</h6>
                <p class="text-danger fw-bold mb-3" id="batalkanNamaSiswa">-</p>
                <div class="alert alert-danger bg-light border-danger text-start small mb-0">
                    <ul class="mb-0">
                        <li>Status validasi Ketua PKBM akan <strong>direset</strong></li>
                        <li>Validasi Bendahara (jika sudah ada) juga ikut direset</li>
                        <li>Wali kelas perlu mengirim ulang rapor</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="batalkanForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Validasi Terpilih --}}
<div class="modal fade" id="validasiTerpilihModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-check me-2"></i>Validasi Siswa Terpilih
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fas fa-users fa-3x text-info mb-3"></i>
                <h6 class="fw-bold mb-1">Validasi <span id="jumlahTerpilih" class="text-info">0</span> siswa yang dipilih?</h6>
                <p class="text-muted small mb-3">Semua siswa yang dicentang akan langsung divalidasi oleh Ketua PKBM.</p>
                <div class="alert alert-info bg-light border-info text-start small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Rapor yang divalidasi akan diteruskan ke Bendahara untuk verifikasi keuangan.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-info" id="btnKonfirmasiTerpilih">
                    <i class="fas fa-check me-1"></i> Ya, Validasi Terpilih
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Validasi Semua --}}
<div class="modal fade" id="validasiSemuaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-check-double me-2"></i>Validasi Semua Rapor Pending
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                <h6 class="fw-bold mb-2">Validasi semua siswa yang masih pending?</h6>
                <p class="text-muted small mb-3">Seluruh rapor yang sudah dikirim wali kelas dan belum divalidasi akan langsung disetujui.</p>
                <div class="alert alert-warning bg-light border-warning text-start small mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Pastikan Anda sudah meninjau semua rapor sebelum melakukan validasi massal.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form action="{{ route('ketua.validasi-rapor.validasi-semua') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-double me-1"></i> Ya, Validasi Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Peringatan tidak ada yang dipilih --}}
<div class="modal fade" id="peringatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Perhatian
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-mouse-pointer fa-3x text-warning mb-3"></i>
                <p class="fw-bold mb-0">Pilih minimal 1 siswa terlebih dahulu.</p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Minta Revisi --}}
<div class="modal fade" id="revisiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="revisiForm" method="POST">
                @csrf
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-edit me-2"></i>Minta Revisi Rapor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-2">Kirim catatan revisi ke wali kelas untuk: <strong id="revisiNamaSiswa">-</strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan Revisi</label>
                        <textarea name="catatan_revisi" class="form-control" rows="4" required placeholder="Tuliskan apa yang perlu diperbaiki oleh wali kelas..."></textarea>
                    </div>
                    <div class="alert alert-warning bg-light border-warning text-start small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Rapor akan dikembalikan ke wali kelas. Status validasi wali & ketua akan direset.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold"><i class="fas fa-paper-plane me-1"></i> Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/ketua/validasi-rapor/index.js'])
@endsection
