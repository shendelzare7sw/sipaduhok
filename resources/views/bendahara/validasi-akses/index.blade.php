@extends('layouts.sneat')

@section('title', 'Validasi Akses')
@section('page-title', 'Validasi Akses Ujian & Rapor')
@section('page-subtitle', 'Validasi akses berdasarkan status pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
    border: none;
}
.stat-card:hover { transform: translateY(-5px); }
.stat-content { position: relative; z-index: 2; }
.stat-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 8px; }
.stat-number { font-size: 28px; font-weight: 700; margin-bottom: 4px; line-height: 1.2; }
.stat-label-sub { font-size: 13px; opacity: 0.8; }
.stat-icon-bg { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 60px; opacity: 0.15; z-index: 1; }

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
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- STATISTIK --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Siswa Aktif</div>
                    <div class="stat-number">{{ $totalSiswa }}</div>
                    <div class="stat-label-sub">Siswa Terdaftar</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-user-graduate"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Akses Ujian Valid</div>
                    <div class="stat-number">{{ $validasiUjian }}</div>
                    <div class="stat-label-sub">Sudah Divalidasi</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-file-signature"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Akses Rapor Valid</div>
                    <div class="stat-number">{{ $validasiRapor }}</div>
                    <div class="stat-label-sub">Sudah Divalidasi</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
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

    {{-- FILTER & MASS ACTIONS --}}
    <div class="card shadow mb-4" style="position: relative; z-index: 99;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <form action="{{ route('bendahara.validasi-akses.index') }}" method="GET" class="d-flex gap-2">
                        {{-- Filter Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false" 
                                data-bs-auto-close="outside" data-bs-display="static">
                                <i class="fas fa-filter me-1"></i> Filter Data
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="filterDropdown" style="min-width: 300px; z-index: 9999;">
                                <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>
                                
                                {{-- Filter Cabang --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Cabang</label>
                                    <select name="cabang_id" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangList as $cabang)
                                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Jenjang --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Jenjang</label>
                                    <select name="jenjang" class="form-select form-select-sm">
                                        <option value="">Semua Jenjang</option>
                                        @foreach($jenjangList as $jenjang)
                                            <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>
                                                {{ $jenjang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Kelas --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Kelas</label>
                                    <select name="kelas_id" class="form-select form-select-sm">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Status Ujian --}}
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Status Ujian</label>
                                    <select name="status_ujian" class="form-select form-select-sm">
                                        <option value="">Semua</option>
                                        <option value="valid" {{ request('status_ujian') == 'valid' ? 'selected' : '' }}>Valid</option>
                                        <option value="belum" {{ request('status_ujian') == 'belum' ? 'selected' : '' }}>Belum</option>
                                    </select>
                                </div>

                                {{-- Filter Status Rapor --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Status Rapor</label>
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

                        {{-- Pencarian (Tetap di luar) --}}
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama/NISN..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 text-end">
                    <button type="button" class="btn btn-success btn-sm shadow-sm fw-bold" onclick="bulkValidasiUjian()">
                        <i class="fas fa-check-double me-1"></i> Validasi Ujian
                    </button>
                    <button type="button" class="btn btn-info btn-sm shadow-sm fw-bold ms-1" onclick="bulkValidasiRapor()">
                        <i class="fas fa-check-double me-1"></i> Validasi Rapor
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL SISWA --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-user-shield me-2"></i>Daftar Kendali Akses Siswa</h6>
        </div>
        <div class="card-body p-0">
            <form id="bulk-form" action="{{ route('bendahara.validasi-akses.bulk-validasi-selected') }}" method="POST">
                @csrf
                <input type="hidden" name="action" id="bulk-action" value="">

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
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
                                    <td class="text-center align-middle"><input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="siswa-checkbox form-check-input"></td>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $siswa->firstItem() + $index }}</td>
                                    <td class="align-middle text-start">
                                        <div class="fw-bold text-gray-900">{{ $s->nama_lengkap }}</div>
                                        <small class="text-muted fw-bold">{{ $s->nisn }} | {{ $s->cabang->nama_cabang ?? '-' }}</small>
                                    </td>
                                    <td class="text-center align-middle fw-bold text-primary small">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="align-middle currency-font">Rp {{ number_format($s->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="align-middle currency-font {{ $s->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                        Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($s->validasi_ujian_bendahara)
                                            <span class="badge bg-success badge-status shadow-sm"><i class="fas fa-check-circle"></i> VALID</span>
                                            <div class="text-xs text-muted mt-1">{{ \Carbon\Carbon::parse($s->tanggal_validasi_ujian_bendahara)->format('d/m/Y') }}</div>
                                        @else
                                            <span class="badge bg-warning badge-status text-white shadow-sm">⏳ BELUM</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($s->validasi_rapor_bendahara)
                                            <span class="badge bg-success badge-status shadow-sm"><i class="fas fa-check-circle"></i> VALID</span>
                                            <div class="text-xs text-muted mt-1">{{ \Carbon\Carbon::parse($s->tanggal_validasi_rapor_bendahara)->format('d/m/Y') }}</div>
                                        @else
                                            <span class="badge bg-warning badge-status text-white shadow-sm">⏳ BELUM</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group gap-1">
                                            @if(!$s->validasi_ujian_bendahara)
                                                <button form="none" onclick="confirmAction('{{ route('bendahara.validasi-akses.validasi-ujian', $s->id) }}', 'Validasi ujian {{ $s->nama_lengkap }}?')" class="btn btn-sm btn-success rounded-circle" title="Validasi Ujian"><i class="fas fa-check"></i></button>
                                            @else
                                                <button form="none" onclick="confirmAction('{{ route('bendahara.validasi-akses.batalkan-ujian', $s->id) }}', 'Batalkan validasi ujian?')" class="btn btn-sm btn-outline-danger rounded-circle" title="Batal Ujian"><i class="fas fa-undo"></i></button>
                                            @endif

                                            @if(!$s->validasi_rapor_bendahara)
                                                <button form="none" onclick="confirmAction('{{ route('bendahara.validasi-akses.validasi-rapor', $s->id) }}', 'Validasi rapor {{ $s->nama_lengkap }}?')" class="btn btn-sm btn-info rounded-circle" title="Validasi Rapor"><i class="fas fa-check"></i></button>
                                            @else
                                                <button form="none" onclick="confirmAction('{{ route('bendahara.validasi-akses.batalkan-rapor', $s->id) }}', 'Batalkan validasi rapor?')" class="btn btn-sm btn-outline-danger rounded-circle" title="Batal Rapor"><i class="fas fa-undo"></i></button>
                                            @endif

                                            <a href="{{ route('bendahara.tagihan.show', $s->id) }}" class="btn btn-sm btn-secondary rounded-circle" title="Detail Tagihan"><i class="fas fa-file-invoice"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center py-5 text-muted fst-italic">Data tidak ditemukan</td></tr>
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
            <div class="row">
                @foreach($kelasList->take(6) as $kelas)
                    <div class="col-md-4 mb-3">
                        <div class="card border-start border-primary border-4 shadow-sm h-100">
                            <div class="card-body py-3">
                                <div class="fw-bold text-gray-800 mb-1">{{ $kelas->nama_kelas }}</div>
                                <div class="text-xs text-muted mb-3">{{ $kelas->jenjang }} • {{ $kelas->siswa->count() }} Siswa</div>
                                <div class="row g-2">
                                    <div class="col">
                                        <form id="form-ujian-{{ $kelas->id }}" action="{{ route('bendahara.validasi-akses.bulk-validasi-ujian', $kelas->id) }}" method="POST">
                                            @csrf
                                            <button type="button" class="btn btn-success btn-sm w-100 fw-bold" onclick="confirmClassAction('form-ujian-{{ $kelas->id }}', 'Validasi Ujian Se-Kelas', 'Validasi ujian untuk seluruh siswa di kelas {{ $kelas->nama_kelas }}?')">UJIAN</button>
                                        </form>
                                    </div>
                                    <div class="col">
                                        <form id="form-rapor-{{ $kelas->id }}" action="{{ route('bendahara.validasi-akses.bulk-validasi-rapor', $kelas->id) }}" method="POST">
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

document.getElementById('confirmBtn').addEventListener('click', function() {
    if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
    }
    confirmModal.hide();
});

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
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
        function() {
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
    showConfirmModal(
        'Validasi Akses Rapor',
        'Validasi akses rapor untuk ' + checked.length + ' siswa terpilih?',
        function() {
            document.getElementById('bulk-action').value = 'rapor';
            document.getElementById('bulk-form').submit();
        }
    );
}

function confirmAction(url, message) {
    showConfirmModal(
        'Konfirmasi Aksi',
        message,
        function() {
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
        function() {
            document.getElementById(formId).submit();
        }
    );
}
</script>
@endsection