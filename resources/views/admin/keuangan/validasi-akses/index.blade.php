@extends('layouts.sneat')

@section('title', 'Validasi Akses')
@section('page-title', 'Validasi Akses Ujian & Rapor')
@section('page-subtitle', 'Validasi akses berdasarkan status pembayaran siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.stat-card {
    padding: 20px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
    border: none;
}
.stat-card:hover { transform: translateY(-3px); }
.stat-content { position: relative; z-index: 2; }
.stat-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 6px; }
.stat-number { font-size: 26px; font-weight: 700; margin-bottom: 2px; line-height: 1.2; }
.stat-label-sub { font-size: 12px; opacity: 0.8; }
.stat-icon-bg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 50px; opacity: 0.15; z-index: 1; }

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

.table thead th {
    background: #f8f9fc;
    color: #4e73df;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e3e6f0;
    text-align: center;
}
.badge-status { padding: 6px 12px; border-radius: 50px; font-weight: 700; font-size: 11px; }
.currency-font { font-family: 'Nunito', sans-serif; font-weight: 700; }

/* Mobile responsive */
@media (max-width: 767.98px) {
    .stat-card { padding: 14px; }
    .stat-number { font-size: 22px; }
    .stat-icon-bg { font-size: 36px; right: 8px; }
    .mass-action-buttons { flex-direction: column; width: 100%; }
    .mass-action-buttons .btn { width: 100%; font-size: 12px; }
    .filter-row { flex-direction: column !important; }
    .filter-row .dropdown, .filter-row .input-group { width: 100%; }
    .table thead th { font-size: 9px; padding: 6px 4px; white-space: nowrap; }
    .table td { font-size: 12px; padding: 6px 4px; }
    .badge-status { padding: 4px 8px; font-size: 10px; }
    .currency-font { font-size: 12px; }
    .btn-group.gap-1 .btn { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .quick-val-cards .col-md-4 { flex: 0 0 100%; max-width: 100%; }
}
@media (max-width: 575.98px) {
    .stat-number { font-size: 20px; }
    .stat-title { font-size: 10px; }
    .stat-label-sub { font-size: 11px; }
}
</style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">

            {{-- ALUR INFO --}}
            <div class="alert alert-light border border-primary border-opacity-25 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <small class="text-muted fw-bold text-uppercase">Alur Validasi Akses:</small>
                    <span class="badge bg-warning text-white"><i class="fas fa-user-check me-1"></i>1. Ketua Approve Rapor</span>
                    <i class="fas fa-arrow-right text-muted small d-none d-sm-inline"></i>
                    <span class="badge bg-primary"><i class="fas fa-money-bill me-1"></i>2. Admin/Bendahara Validasi</span>
                    <i class="fas fa-arrow-right text-muted small d-none d-sm-inline"></i>
                    <span class="badge bg-success"><i class="fas fa-unlock me-1"></i>3. Akses Terbuka</span>
                </div>
                <div class="mt-2 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Ujian:</strong> Otomatis terbuka jika lunas (sesuai Pengaturan Batas Pembayaran) atau dispensasi disetujui Ketua.
                    <strong>Rapor:</strong> Bisa divalidasi hanya jika Ketua PKBM sudah approve.
                    Siswa belum lunas? Gunakan tombol <strong>Ajukan Dispensasi</strong> untuk mengirim ke Ketua.
                </div>
            </div>

            {{-- STATISTIK --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 col-6 mb-3">
                    <div class="stat-card bg-gradient-blue">
                        <div class="stat-content">
                            <div class="stat-title">Total Siswa Aktif</div>
                            <div class="stat-number">{{ $totalSiswa }}</div>
                            <div class="stat-label-sub">Siswa Terdaftar</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-user-graduate"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6 mb-3">
                    <div class="stat-card bg-gradient-green">
                        <div class="stat-content">
                            <div class="stat-title">Akses Ujian Valid</div>
                            <div class="stat-number">{{ $validasiUjian }}</div>
                            <div class="stat-label-sub">Sudah Divalidasi</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-file-signature"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6 mb-3">
                    <div class="stat-card bg-gradient-purple">
                        <div class="stat-content">
                            <div class="stat-title">Akses Rapor Valid</div>
                            <div class="stat-number">{{ $validasiRapor }}</div>
                            <div class="stat-label-sub">Sudah Divalidasi</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-file-invoice"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6 mb-3">
                    <div class="stat-card bg-gradient-orange">
                        <div class="stat-content">
                            <div class="stat-title">Belum Divalidasi</div>
                            <div class="stat-number">{{ $belumValidasi }}</div>
                            <div class="stat-label-sub">Menunggu Antrean</div>
                        </div>
                        <div class="stat-icon-bg"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="card shadow mb-3" style="position: relative; z-index: 99;">
                <div class="card-body py-2">
                    <form action="{{ route('admin.keuangan.validasi-akses.index') }}" method="GET" class="d-flex gap-2 flex-wrap filter-row">
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                data-bs-auto-close="outside" data-bs-display="static">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="filterDropdown" style="min-width: 280px; z-index: 9999;">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Cabang</label>
                                    <select name="cabang_id" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangList as $cabang)
                                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Jenjang</label>
                                    <select name="jenjang" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang</option>
                                        @foreach($jenjangList as $jenjang)
                                            <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Kelas</label>
                                    <select name="kelas_id" class="form-select form-select-sm">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Status Ujian</label>
                                    <select name="status_ujian" class="form-select form-select-sm">
                                        <option value="">Semua</option>
                                        <option value="valid" {{ request('status_ujian') == 'valid' ? 'selected' : '' }}>Valid</option>
                                        <option value="belum" {{ request('status_ujian') == 'belum' ? 'selected' : '' }}>Belum</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold mb-1">Status Rapor</label>
                                    <select name="status_rapor" class="form-select form-select-sm">
                                        <option value="">Semua</option>
                                        <option value="valid" {{ request('status_rapor') == 'valid' ? 'selected' : '' }}>Valid</option>
                                        <option value="belum" {{ request('status_rapor') == 'belum' ? 'selected' : '' }}>Belum</option>
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="input-group flex-grow-1" style="min-width: 150px;">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama/NISN..." value="{{ request('search') }}">
                            <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MASS ACTIONS --}}
            <div class="card shadow mb-4">
                <div class="card-body py-2">
                    <div class="d-flex gap-2 flex-wrap mass-action-buttons">
                        <button type="button" class="btn btn-success btn-sm shadow-sm fw-bold" onclick="bulkValidasiUjian()">
                            <i class="fas fa-check-double me-1"></i> Validasi Ujian
                        </button>
                        <button type="button" id="btn-bulk-rapor" class="btn btn-outline-secondary btn-sm shadow-sm fw-bold" onclick="bulkValidasiRapor()" title="Belum ada siswa yang di-approve Ketua">
                            <i class="fas fa-lock me-1" id="btn-bulk-rapor-icon"></i> Validasi Rapor <small class="opacity-75" id="btn-bulk-rapor-label">(Perlu Ketua)</small>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#dispensasiModal">
                            <i class="fas fa-hand-holding-heart me-1"></i> Ajukan Dispensasi
                            @if(($dispensasiPending ?? 0) > 0)
                                <span class="badge bg-danger ms-1">{{ $dispensasiPending }}</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>

            {{-- TABEL SISWA --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-user-shield me-2"></i>Daftar Kendali Akses Siswa
                    </h6>
                </div>
                <div class="card-body p-0">
                    <form id="bulk-form" action="{{ route('admin.keuangan.validasi-akses.bulk-validasi-selected') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="tipe" id="bulk-action" value="">

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="select-all" onclick="toggleSelectAll()">
                                        </th>
                                        <th width="50">NO</th>
                                        <th class="text-start">IDENTITAS SISWA</th>
                                        <th>KELAS</th>
                                        <th>TAGIHAN</th>
                                        <th>SISA</th>
                                        <th>AKSES UJIAN</th>
                                        <th>AKSES RAPOR</th>
                                        <th>AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $index => $s)
                                        <tr>
                                            <td class="text-center align-middle"><input type="checkbox" name="siswa_ids[]"
                                                    value="{{ $s->id }}" class="siswa-checkbox form-check-input" data-ketua-approved="{{ $s->validasi_rapor_ketua ? '1' : '0' }}"></td>
                                            <td class="text-center align-middle fw-bold text-gray-600">
                                                {{ $siswa->firstItem() + $index }}</td>
                                            <td class="align-middle text-start">
                                                <div class="fw-bold text-gray-900">{{ $s->nama_lengkap }}</div>
                                                <small class="text-muted fw-bold">{{ $s->nisn }} |
                                                    {{ $s->cabang->nama_cabang ?? '-' }}</small>
                                            </td>
                                            <td class="text-center align-middle fw-bold text-primary small">
                                                {{ $s->kelas->nama_kelas ?? '-' }}</td>
                                            <td class="align-middle currency-font">Rp
                                                {{ number_format($s->total_tagihan, 0, ',', '.') }}</td>
                                            <td
                                                class="align-middle currency-font {{ $s->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($s->validasi_ujian_bendahara)
                                                    <span class="badge bg-success badge-status shadow-sm"><i
                                                            class="fas fa-check-circle"></i> VALID</span>
                                                    <div class="text-xs text-muted mt-1">
                                                        {{ \Carbon\Carbon::parse($s->tanggal_validasi_ujian_bendahara)->format('d/m/Y') }}
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning badge-status text-white shadow-sm"><i class="fas fa-clock"></i> BELUM</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($s->validasi_rapor_bendahara)
                                                    <span class="badge bg-success badge-status shadow-sm"><i
                                                            class="fas fa-check-circle"></i> VALID</span>
                                                    <div class="text-xs text-muted mt-1">
                                                        {{ \Carbon\Carbon::parse($s->tanggal_validasi_rapor_bendahara)->format('d/m/Y') }}
                                                    </div>
                                                @elseif(!$s->validasi_rapor_ketua)
                                                    <span class="badge bg-secondary badge-status shadow-sm"><i class="fas fa-hourglass-half"></i> TUNGGU KETUA</span>
                                                @else
                                                    <span class="badge bg-warning badge-status text-white shadow-sm"><i class="fas fa-clock"></i> BELUM</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group gap-1">
                                                    @if(!$s->validasi_ujian_bendahara)
                                                        <button form="none"
                                                            onclick="confirmAction('{{ route('admin.keuangan.validasi-akses.validasi-ujian', $s->id) }}', 'Validasi ujian {{ $s->nama_lengkap }}?')"
                                                            class="btn btn-sm btn-success rounded-circle" title="Validasi Ujian"><i
                                                                class="fas fa-check"></i></button>
                                                    @else
                                                        <button form="none"
                                                            onclick="confirmAction('{{ route('admin.keuangan.validasi-akses.batalkan-ujian', $s->id) }}', 'Batalkan validasi ujian?')"
                                                            class="btn btn-sm btn-outline-danger rounded-circle"
                                                            title="Batal Ujian"><i class="fas fa-undo"></i></button>
                                                    @endif

                                                    @if(!$s->validasi_rapor_bendahara)
                                                        @if($s->validasi_rapor_ketua)
                                                            <button form="none"
                                                                onclick="confirmAction('{{ route('admin.keuangan.validasi-akses.validasi-rapor', $s->id) }}', 'Validasi rapor {{ $s->nama_lengkap }}?')"
                                                                class="btn btn-sm btn-info rounded-circle" title="Validasi Rapor"><i
                                                                    class="fas fa-check"></i></button>
                                                        @else
                                                            <button class="btn btn-sm btn-light rounded-circle border" disabled title="Menunggu validasi Ketua"><i class="fas fa-lock text-muted"></i></button>
                                                        @endif
                                                    @else
                                                        <button form="none"
                                                            onclick="confirmAction('{{ route('admin.keuangan.validasi-akses.batalkan-rapor', $s->id) }}', 'Batalkan validasi rapor?')"
                                                            class="btn btn-sm btn-outline-danger rounded-circle"
                                                            title="Batal Rapor"><i class="fas fa-undo"></i></button>
                                                    @endif

                                                    <a href="{{ route('admin.keuangan.tagihan.show', $s->id) }}"
                                                        class="btn btn-sm btn-secondary rounded-circle"
                                                        title="Detail Tagihan"><i class="fas fa-file-invoice"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted fst-italic">Data tidak ditemukan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="px-4 py-3 bg-light border-top">
                        <div class="d-flex justify-content-center">
                            {{ $siswa->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUICK VALIDATION CARDS --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-bolt me-2 text-warning"></i>Validasi Kilat Per Kelas</h6>
                </div>
                <div class="card-body">
                    <div class="row quick-val-cards">
                        @foreach($kelasList->take(6) as $kelas)
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="card border-start border-primary border-4 shadow-sm h-100">
                                    <div class="card-body py-3">
                                        <div class="fw-bold text-gray-800 mb-1">{{ $kelas->nama_kelas }}</div>
                                        <div class="text-xs text-muted mb-3">{{ $kelas->jenjang }} • {{ $kelas->siswa->count() }} Siswa</div>
                                        <div class="row g-2">
                                            <div class="col">
                                                <form id="form-ujian-{{ $kelas->id }}" action="{{ route('admin.keuangan.validasi-akses.bulk-validasi-ujian', $kelas->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-success btn-sm w-100 fw-bold" onclick="confirmClassAction('form-ujian-{{ $kelas->id }}', 'Validasi Ujian Se-Kelas', 'Validasi ujian untuk seluruh siswa di kelas {{ $kelas->nama_kelas }}?')">UJIAN</button>
                                                </form>
                                            </div>
                                            <div class="col">
                                                <form id="form-rapor-{{ $kelas->id }}" action="{{ route('admin.keuangan.validasi-akses.bulk-validasi-rapor', $kelas->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="btn btn-info btn-sm w-100 fw-bold" onclick="confirmClassAction('form-rapor-{{ $kelas->id }}', 'Validasi Rapor Se-Kelas', 'Validasi rapor untuk seluruh siswa di kelas {{ $kelas->nama_kelas }}?')">RAPOR</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    {{-- MODAL KONFIRMASI --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <span id="modalTitle">Konfirmasi Aksi</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0 text-gray-800" id="modalMessage">Apakah Anda yakin?</p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning fw-bold" id="confirmBtn">
                        <i class="fas fa-check me-1"></i> Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ALERT/PERINGATAN --}}
    <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                        <span id="alertTitle">Peringatan</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0 text-gray-800" id="alertMessage">Terjadi kesalahan!</p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-primary fw-bold" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i> Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DISPENSASI --}}
    <div class="modal fade" id="dispensasiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.keuangan.validasi-akses.dispensasi') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="fas fa-hand-holding-heart text-warning me-2"></i>Ajukan Dispensasi ke Ketua PKBM
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Ajukan dispensasi untuk siswa terpilih yang belum lunas agar tetap mendapat akses ujian/rapor. Pengajuan akan dikirim ke Ketua PKBM untuk persetujuan.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tipe Dispensasi</label>
                            <select name="tipe" class="form-select form-select-sm" required>
                                <option value="ujian">Akses Ujian</option>
                                <option value="rapor">Akses Rapor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Periode</label>
                            <select name="periode" class="form-select form-select-sm">
                                <option value="">-- Semua Periode --</option>
                                <option value="pts_ganjil">PTS Ganjil</option>
                                <option value="pas_ganjil">PAS Ganjil</option>
                                <option value="pts_genap">PTS Genap</option>
                                <option value="pas_genap">PAS Genap</option>
                                <option value="ujian_akhir">Ujian Akhir</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alasan Dispensasi</label>
                            <textarea name="alasan" class="form-control form-control-sm" rows="3" required placeholder="Contoh: Siswa memiliki cicilan yang sedang berjalan..."></textarea>
                        </div>
                        <div class="alert alert-info small py-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i> Siswa yang dipilih (centang) di tabel akan dimasukkan ke pengajuan dispensasi.
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <div id="dispensasi-siswa-ids"></div>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-bold" id="submitDispensasi"><i class="fas fa-paper-plane me-1"></i> Kirim ke Ketua</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Inject selected siswa IDs into dispensasi form
    document.getElementById('dispensasiModal').addEventListener('show.bs.modal', function() {
        const container = document.getElementById('dispensasi-siswa-ids');
        container.innerHTML = '';
        const checked = document.querySelectorAll('.siswa-checkbox:checked');
        if (checked.length === 0) {
            document.getElementById('submitDispensasi').disabled = true;
            container.innerHTML = '<span class="text-danger small">Pilih siswa terlebih dahulu!</span>';
        } else {
            document.getElementById('submitDispensasi').disabled = false;
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'siswa_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
        }
    });
    </script>

    <script>
        {{-- Hidden Form untuk Aksi Baris --}}
        const actionForm = document.createElement('form');
        actionForm.id = 'action-form';
        actionForm.method = 'POST';
        actionForm.style.display = 'none';
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        actionForm.appendChild(csrfInput);
        document.body.appendChild(actionForm);

        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
        let confirmCallback = null;

        function showConfirmModal(title, message, callback) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            confirmCallback = callback;
            confirmModal.show();
        }

        function showAlertModal(title, message) {
            document.getElementById('alertTitle').textContent = title;
            document.getElementById('alertMessage').textContent = message;
            alertModal.show();
        }

        document.getElementById('confirmBtn').addEventListener('click', function () {
            if (confirmCallback) {
                confirmCallback();
                confirmCallback = null;
            }
            confirmModal.hide();
        });

        function updateBtnValidasiRapor() {
            const checked = document.querySelectorAll('.siswa-checkbox:checked');
            const hasEligible = Array.from(checked).some(cb => cb.dataset.ketuaApproved === '1');
            const btn = document.getElementById('btn-bulk-rapor');
            const icon = document.getElementById('btn-bulk-rapor-icon');
            const label = document.getElementById('btn-bulk-rapor-label');
            if (hasEligible) {
                btn.className = 'btn btn-info btn-sm shadow-sm fw-bold text-white';
                btn.title = 'Validasi akses rapor siswa yang sudah di-approve Ketua';
                icon.className = 'fas fa-file-alt me-1';
                label.style.display = 'none';
            } else {
                btn.className = 'btn btn-outline-secondary btn-sm shadow-sm fw-bold';
                btn.title = 'Belum ada siswa yang di-approve Ketua';
                icon.className = 'fas fa-lock me-1';
                label.style.display = '';
            }
        }

        document.querySelectorAll('.siswa-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBtnValidasiRapor);
        });

        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.siswa-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBtnValidasiRapor();
        }

        function bulkValidasiUjian() {
            const checked = document.querySelectorAll('.siswa-checkbox:checked');
            if (checked.length === 0) {
                showAlertModal('Data Belum Dipilih', 'Silakan pilih minimal satu siswa terlebih dahulu!');
                return;
            }
            showConfirmModal(
                'Validasi Akses Ujian',
                'Validasi akses ujian untuk ' + checked.length + ' siswa terpilih?',
                function () {
                    document.getElementById('bulk-action').value = 'ujian';
                    document.getElementById('bulk-form').submit();
                }
            );
        }

        function bulkValidasiRapor() {
            const checked = document.querySelectorAll('.siswa-checkbox:checked');
            if (checked.length === 0) {
                showAlertModal('Data Belum Dipilih', 'Silakan pilih minimal satu siswa terlebih dahulu!');
                return;
            }
            const eligible = Array.from(checked).filter(cb => cb.dataset.ketuaApproved === '1');
            const notEligible = checked.length - eligible.length;

            if (eligible.length === 0) {
                showAlertModal('Tidak Bisa Validasi Rapor', 'Semua siswa terpilih belum di-approve Ketua PKBM. Validasi rapor hanya bisa dilakukan setelah Ketua menyetujui.');
                return;
            }

            let msg = 'Validasi akses rapor untuk ' + eligible.length + ' siswa yang sudah di-approve Ketua?';
            if (notEligible > 0) {
                msg += ' (' + notEligible + ' siswa dilewati karena belum di-approve Ketua)';
            }

            showConfirmModal(
                'Validasi Akses Rapor',
                msg,
                function () {
                    document.getElementById('bulk-action').value = 'rapor';
                    document.getElementById('bulk-form').submit();
                }
            );
        }

        function confirmAction(url, message) {
            showConfirmModal(
                'Konfirmasi Aksi',
                message,
                function () {
                    const form = document.getElementById('action-form');
                    form.action = url;
                    form.submit();
                }
            );
        }

        function confirmClassAction(formId, title, message) {
            showConfirmModal(
                title,
                message,
                function () {
                    document.getElementById(formId).submit();
                }
            );
        }
    </script>
@endsection