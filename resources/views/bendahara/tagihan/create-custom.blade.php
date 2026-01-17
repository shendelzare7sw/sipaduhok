@extends('layouts.sneat')

@section('title', 'Tambah Tagihan Custom')
@section('page-title', 'Tambah Tagihan Custom')
@section('page-subtitle', 'Input tagihan khusus untuk beberapa siswa sekaligus')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.siswa-table thead th {
    background: #f8f9fc;
    color: #4e73df;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e3e6f0;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.siswa-table tbody tr:hover {
    background: #f8f9fa;
}

.siswa-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.selected-count-badge {
    font-size: 14px;
    padding: 8px 16px;
}

.filter-section input:focus,
.filter-section select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    <div class="container-fluid px-0">

        {{-- Breadcrumb --}}
        <div class="mb-3">
            <a href="{{ route('bendahara.tagihan.index') }}" class="text-primary text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
            </a>
        </div>

        {{-- Info Alert --}}
        <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4">
            <div class="d-flex">
                <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                <div>
                    <strong>Tagihan Custom Multi-Siswa</strong>
                    <p class="mb-0 mt-1">Pilih beberapa siswa menggunakan checkbox, lalu tentukan jenis tagihan dan nominalnya. Tagihan akan diterapkan ke semua siswa yang dipilih.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('bendahara.tagihan.store-custom') }}" method="POST" id="customTagihanForm">
            @csrf

            <div class="row">
                {{-- Left Column - Student Selection --}}
                <div class="col-lg-7 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-users me-2"></i>Pilih Siswa
                            </h6>
                            <span class="badge bg-primary selected-count-badge" id="selectedCount">0 siswa dipilih</span>
                        </div>
                        <div class="card-body p-0">
                            {{-- Filter Section --}}
                            <div class="p-3 bg-light border-bottom filter-section">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <select id="filterCabang" class="form-select form-select-sm">
                                            <option value="">Semua Cabang</option>
                                            @foreach($cabangList as $cabang)
                                                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="filterKelas" class="form-select form-select-sm">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}" data-cabang="{{ $kelas->cabang_id }}">
                                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" id="searchSiswa" class="form-control form-control-sm" placeholder="Cari nama/NISN...">
                                    </div>
                                </div>
                            </div>

                            {{-- Student Table --}}
                            <div style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-hover mb-0 siswa-table">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAll" class="siswa-checkbox" onclick="toggleSelectAll()">
                                            </th>
                                            <th width="50">NO</th>
                                            <th class="text-start">NAMA SISWA</th>
                                            <th>NISN</th>
                                            <th>KELAS</th>
                                            <th>CABANG</th>
                                        </tr>
                                    </thead>
                                    <tbody id="siswaTableBody">
                                        @foreach($siswaList as $index => $siswa)
                                            <tr class="siswa-row"
                                                data-cabang="{{ $siswa->cabang_id }}"
                                                data-kelas="{{ $siswa->kelas_id }}"
                                                data-search="{{ strtolower($siswa->nama_lengkap . ' ' . $siswa->nisn) }}">
                                                <td class="text-center">
                                                    <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" class="siswa-checkbox" onchange="updateSelectedCount()">
                                                </td>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $siswa->nama_lengkap }}</strong>
                                                </td>
                                                <td class="text-center">{{ $siswa->nisn }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <small class="text-muted">{{ $siswa->cabang->nama_cabang ?? '-' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-3 bg-light border-top">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Total: <strong>{{ $siswaList->count() }}</strong> siswa aktif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Tagihan Details --}}
                <div class="col-lg-5 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 fw-bold text-success">
                                <i class="fas fa-file-invoice-dollar me-2"></i>Detail Tagihan
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Jenis Tagihan Custom --}}
                            <div class="mb-3">
                                <label for="jenis_tagihan" class="form-label fw-bold">
                                    Jenis Tagihan <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="jenis_tagihan"
                                       id="jenis_tagihan"
                                       class="form-control @error('jenis_tagihan') is-invalid @enderror"
                                       value="{{ old('jenis_tagihan') }}"
                                       placeholder="Contoh: Bantuan Dana Pendidikan"
                                       required>
                                @error('jenis_tagihan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nama jenis tagihan yang akan ditampilkan</small>
                            </div>

                            {{-- Jumlah Tagihan --}}
                            <div class="mb-3">
                                <label for="jumlah" class="form-label fw-bold">
                                    Jumlah Tagihan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">Rp</span>
                                    <input type="text"
                                           name="jumlah"
                                           id="jumlah"
                                           class="form-control currency-input @error('jumlah') is-invalid @enderror"
                                           value="{{ old('jumlah') }}"
                                           placeholder="0"
                                           required>
                                </div>
                                @error('jumlah')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Tanggal Jatuh Tempo --}}
                            <div class="mb-3">
                                <label for="tanggal_jatuh_tempo" class="form-label fw-bold">
                                    Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="tanggal_jatuh_tempo"
                                       id="tanggal_jatuh_tempo"
                                       class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror"
                                       value="{{ old('tanggal_jatuh_tempo', now()->addMonth()->format('Y-m-d')) }}"
                                       required>
                                @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-bold">
                                    Keterangan / Catatan
                                </label>
                                <textarea name="keterangan"
                                          id="keterangan"
                                          rows="3"
                                          class="form-control @error('keterangan') is-invalid @enderror"
                                          placeholder="Opsional: Catatan tambahan tentang tagihan ini">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Preview Box --}}
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Perhatian:</strong> Tagihan akan dibuat untuk <span id="previewCount" class="fw-bold text-danger">0</span> siswa yang dipilih.
                            </div>

                            {{-- Info Tahun Ajaran --}}
                            <div class="text-muted small mb-3">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2">
                                <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-secondary flex-fill">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="button" class="btn btn-primary flex-fill fw-bold" onclick="submitForm()">
                                    <i class="fas fa-save me-1"></i> Simpan Tagihan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterCabang = document.getElementById('filterCabang');
    const filterKelas = document.getElementById('filterKelas');
    const searchInput = document.getElementById('searchSiswa');

    // Filter function
    function filterSiswa() {
        const cabangId = filterCabang.value;
        const kelasId = filterKelas.value;
        const searchTerm = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('.siswa-row');

        rows.forEach(row => {
            const rowCabang = row.getAttribute('data-cabang');
            const rowKelas = row.getAttribute('data-kelas');
            const rowSearch = row.getAttribute('data-search');

            let show = true;

            if (cabangId && rowCabang !== cabangId) show = false;
            if (kelasId && rowKelas !== kelasId) show = false;
            if (searchTerm && !rowSearch.includes(searchTerm)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    // Filter kelas based on cabang
    filterCabang.addEventListener('change', function() {
        const selectedCabang = this.value;
        const kelasOptions = filterKelas.querySelectorAll('option');

        kelasOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                return;
            }

            const kelasCabang = option.getAttribute('data-cabang');
            option.style.display = (!selectedCabang || kelasCabang === selectedCabang) ? '' : 'none';
        });

        if (filterKelas.selectedOptions[0]?.style.display === 'none') {
            filterKelas.value = '';
        }

        filterSiswa();
    });

    filterKelas.addEventListener('change', filterSiswa);
    searchInput.addEventListener('input', filterSiswa);
});

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const visibleCheckboxes = document.querySelectorAll('.siswa-row:not([style*="display: none"]) .siswa-checkbox:not(#selectAll)');
    
    visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.siswa-checkbox:checked:not(#selectAll)').length;
    document.getElementById('selectedCount').textContent = checked + ' siswa dipilih';
    document.getElementById('previewCount').textContent = checked;
}

function submitForm() {
    const checked = document.querySelectorAll('.siswa-checkbox:checked:not(#selectAll)');
    
    if (checked.length === 0) {
        alert('Silakan pilih minimal satu siswa!');
        return;
    }

    const jenisTagihan = document.getElementById('jenis_tagihan').value;
    const jumlah = document.getElementById('jumlah').value;

    if (!jenisTagihan) {
        alert('Silakan isi jenis tagihan!');
        return;
    }

    if (!jumlah || jumlah === '0') {
        alert('Silakan isi jumlah tagihan!');
        return;
    }

    showConfirm({
        title: 'Konfirmasi Tagihan Custom',
        message: `Anda akan membuat tagihan "${jenisTagihan}" dengan nominal Rp ${jumlah} untuk ${checked.length} siswa. Lanjutkan?`,
        type: 'warning',
        confirmText: 'Ya, Simpan',
        onConfirm: function() {
            // Parse currency before submit
            const jumlahInput = document.getElementById('jumlah');
            jumlahInput.value = jumlahInput.value.replace(/\./g, '') || '0';
            document.getElementById('customTagihanForm').submit();
        }
    });
}
</script>
@endsection
