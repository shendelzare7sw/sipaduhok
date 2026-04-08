@extends('layouts.sneat')

@section('title', 'Tambah Tagihan Custom')
@section('page-title', 'Tambah Tagihan Custom')
@section('page-subtitle', 'Input tagihan khusus untuk beberapa siswa sekaligus')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Custom SweetAlert styling to match theme */
        .swal2-popup {
            font-family: 'Public Sans', sans-serif;
            border-radius: 1rem;
        }

        .swal2-title {
            font-size: 1.5rem;
            color: #566a7f;
        }

        .swal2-html-container {
            color: #697a8d;
        }

        .btn-confirm-swal {
            padding: 0.5rem 2rem;
            font-weight: 600;
        }

        /* Desktop Table Styles */
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
            accent-color: #4e73df;
        }

        .selected-count-badge {
            font-size: 14px;
            padding: 8px 16px;
            white-space: nowrap;
        }

        .filter-section input:focus,
        .filter-section select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 992px) {
            .row {
                flex-direction: column;
            }

            .col-lg-7,
            .col-lg-5 {
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            /* Card Headers Responsive */
            .card-header {
                flex-wrap: wrap !important;
            }

            .card-header > div {
                width: 100%;
                display: flex !important;
                justify-content: space-between;
                align-items: center;
                gap: 8px;
            }

            /* Filter Section Mobile */
            .filter-section {
                padding: 12px 8px !important;
            }

            .filter-section .row {
                flex-direction: column !important;
            }

            .filter-section .col-md-3 {
                width: 100% !important;
                max-width: 100%;
            }

            /* Table Responsive */
            .table-responsive {
                border: none !important;
            }

            .table-responsive table {
                border-collapse: separate;
                border-spacing: 0 1rem;
            }

            .siswa-table thead {
                display: none;
            }

            .siswa-table tbody tr {
                display: block;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 1rem;
                padding: 0;
            }

            .siswa-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 0.75rem 1rem;
                border-bottom: 1px dashed #e2e8f0;
            }

            .siswa-table tbody td:first-child {
                padding: 0.75rem;
                justify-content: flex-start;
                align-items: center;
            }

            .siswa-table tbody td:last-child {
                border-bottom: none;
            }

            .siswa-table tbody td::before {
                content: attr(data-label);
                display: block;
                font-weight: 700;
                font-size: 0.75rem;
                color: #64748b;
                text-transform: uppercase;
                margin-right: 1rem;
                text-align: left;
                white-space: nowrap;
            }

            .siswa-table tbody td:first-child::before {
                content: '';
                display: none;
            }

            /* Select All Row Mobile */
            .select-all-row {
                display: none;
                background: #f8f9fc;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 1rem;
                margin-bottom: 1rem;
                align-items: center;
                gap: 12px;
            }

            .select-all-row.mobile-visible {
                display: flex;
            }

            .select-all-row input[type="checkbox"] {
                width: 20px;
                height: 20px;
                cursor: pointer;
            }

            .select-all-row label {
                margin: 0;
                cursor: pointer;
                font-weight: 600;
                color: #4e73df;
                user-select: none;
            }

            .siswa-table tbody td strong {
                text-align: right;
            }

            .siswa-table tbody td .badge {
                font-size: 11px;
                white-space: nowrap;
            }

            /* Counter Badge Mobile */
            .selected-count-badge {
                font-size: 12px !important;
                padding: 6px 12px !important;
                background: #4e73df !important;
                color: white;
            }

            /* Counter Section Mobile */
            .border-bottom.d-flex {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px;
            }

            .border-bottom.d-flex span:first-child {
                font-size: 12px;
            }

            /* Action Buttons Mobile */
            .d-flex.gap-2 {
                flex-wrap: wrap;
            }

            .d-flex.gap-2 .btn {
                flex: 1 1 auto;
                min-width: 120px;
                font-size: 13px;
                padding: 8px 12px;
            }

            /* Alert Responsive */
            .alert {
                font-size: 13px;
            }

            .alert ul li {
                margin-bottom: 4px;
            }

            /* Input Group Mobile */
            .input-group {
                width: 100%;
            }

            .form-select,
            .form-control {
                font-size: 14px;
            }

            /* Textarea Responsive */
            textarea.form-control {
                resize: vertical;
                min-height: 100px;
            }
        }

        /* Extra Small Devices */
        @media (max-width: 480px) {
            .siswa-table tbody td {
                padding: 0.6rem 0.8rem;
                font-size: 12px;
            }

            .siswa-table tbody td::before {
                font-size: 0.7rem;
                margin-right: 0.5rem;
            }

            .d-flex.gap-2 .btn {
                min-width: 100px;
                font-size: 12px;
                padding: 7px 10px;
            }

            .selected-count-badge {
                font-size: 11px !important;
                padding: 4px 8px !important;
            }

            .card-header h6 {
                font-size: 14px;
            }
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">

            {{-- Breadcrumb --}}
            <div class="mb-3">
                <a href="{{ route('admin.keuangan.tagihan.index') }}" class="text-primary text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
                </a>
            </div>

            {{-- Info Alert --}}
            <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                    <div>
                        <strong>Tagihan Custom Multi-Siswa</strong>
                        <p class="mb-0 mt-1">Pilih beberapa siswa menggunakan checkbox, lalu tentukan jenis tagihan dan
                            nominalnya. Tagihan akan diterapkan ke semua siswa yang dipilih.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.keuangan.tagihan.store-custom') }}" method="POST" id="customTagihanForm">
                @csrf

                <div class="row">
                    {{-- Left Column - Student Selection --}}
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                <h6 class="m-0 fw-bold text-primary">
                                    <i class="fas fa-users me-2"></i>Pilih Siswa
                                </h6>
                                <span class="badge bg-primary selected-count-badge" id="selectedCount">0 siswa
                                    dipilih</span>
                            </div>
                            <div class="card-body p-0">
                                {{-- Filter Section --}}
                                <div class="p-3 bg-light border-bottom filter-section">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold mb-1">Cabang <span class="text-danger">*</span></label>
                                            <select id="filterCabang" class="form-select form-select-sm">
                                                <option value="">-- Pilih Cabang --</option>
                                                @foreach($cabangList as $cabang)
                                                    <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold mb-1">Jenjang</label>
                                            <select id="filterJenjang" class="form-select form-select-sm" disabled>
                                                <option value="">-- Pilih Jenjang --</option>
                                                @php
                                                    $jenjangCustomList = $kelasList->pluck('jenjang')->unique()->sort();
                                                @endphp
                                                @foreach($jenjangCustomList as $jenjang)
                                                    <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold mb-1">Kelas</label>
                                            <select id="filterKelas" class="form-select form-select-sm" disabled>
                                                <option value="">-- Pilih Kelas --</option>
                                                @foreach($kelasList as $kelas)
                                                    <option value="{{ $kelas->id }}" data-cabang="{{ $kelas->cabang_id }}" data-jenjang="{{ $kelas->jenjang }}">
                                                        {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold mb-1">Cari Siswa</label>
                                            <input type="text" id="searchSiswa" class="form-control form-control-sm"
                                                placeholder="Cari nama/NISN...">
                                        </div>
                                    </div>
                                </div>

                                {{-- Student Table (Responsive) --}}
                                {{-- Select All Row (Mobile) --}}
                                <div class="select-all-row">
                                    <input type="checkbox" id="selectAll" class="siswa-checkbox form-check-input"
                                        onclick="toggleSelectAll()" style="margin: 0;">
                                    <label for="selectAll" class="form-check-label">Pilih Semua Siswa</label>
                                </div>

                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-hover mb-0 siswa-table">
                                        <thead>
                                            <tr>
                                                <th width="40" class="text-center">
                                                    <input type="checkbox" id="selectAll" class="siswa-checkbox"
                                                        onclick="toggleSelectAll()">
                                                </th>
                                                <th width="50" class="text-center">NO</th>
                                                <th class="text-start">NAMA SISWA</th>
                                                <th class="text-center">NISN</th>
                                                <th class="text-center">KELAS</th>
                                                <th class="text-center">CABANG</th>
                                            </tr>
                                        </thead>
                                        <tbody id="siswaTableBody">
                                            @foreach($siswaList as $index => $siswa)
                                                <tr class="siswa-row" data-cabang="{{ $siswa->cabang_id }}"
                                                    data-kelas="{{ $siswa->kelas_id }}"
                                                    data-jenjang="{{ $siswa->kelas->jenjang ?? '' }}"
                                                    data-search="{{ strtolower($siswa->nama_lengkap . ' ' . $siswa->nisn) }}">
                                                    <td class="text-center" data-label="">
                                                        <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}"
                                                            class="siswa-checkbox" onchange="updateSelectedCount()">
                                                    </td>
                                                    <td class="text-center" data-label="NO">{{ $index + 1 }}</td>
                                                    <td data-label="NAMA SISWA">
                                                        <strong>{{ $siswa->nama_lengkap }}</strong>
                                                    </td>
                                                    <td class="text-center" data-label="NISN">{{ $siswa->nisn }}</td>
                                                    <td class="text-center" data-label="KELAS">
                                                        <span
                                                            class="badge bg-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-center" data-label="CABANG">
                                                        <small
                                                            class="text-muted">{{ $siswa->cabang->nama_cabang ?? '-' }}</small>
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
                                    <input type="text" name="jenis_tagihan" id="jenis_tagihan"
                                        class="form-control @error('jenis_tagihan') is-invalid @enderror"
                                        value="{{ old('jenis_tagihan') }}" placeholder="Contoh: Bantuan Dana Pendidikan"
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
                                        <input type="text" name="jumlah" id="jumlah"
                                            class="form-control currency-input @error('jumlah') is-invalid @enderror"
                                            value="{{ old('jumlah') }}" placeholder="0" required>
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
                                    <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo"
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
                                    <textarea name="keterangan" id="keterangan" rows="3"
                                        class="form-control @error('keterangan') is-invalid @enderror"
                                        placeholder="Opsional: Catatan tambahan tentang tagihan ini">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Preview Box --}}
                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Tagihan akan dibuat untuk <span id="previewCount"
                                        class="fw-bold text-danger">0</span> siswa yang dipilih.
                                </div>

                                {{-- Info Tahun Ajaran --}}
                                <div class="text-muted small mb-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.keuangan.tagihan.index') }}"
                                        class="btn btn-secondary flex-fill">
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


    {{-- Confirm Modal Removed (Replaced by SweetAlert2) --}}

@endsection

@section('scripts')
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterCabang = document.getElementById('filterCabang');
            const filterJenjang = document.getElementById('filterJenjang');
            const filterKelas = document.getElementById('filterKelas');
            const searchInput = document.getElementById('searchSiswa');

            // Filter function - applies all active filters to siswa rows
            function filterSiswa() {
                const cabangId = filterCabang.value;
                const jenjangVal = filterJenjang.value;
                const kelasId = filterKelas.value;
                const searchTerm = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('.siswa-row');

                rows.forEach(row => {
                    const rowCabang = row.getAttribute('data-cabang');
                    const rowKelas = row.getAttribute('data-kelas');
                    const rowJenjang = row.getAttribute('data-jenjang');
                    const rowSearch = row.getAttribute('data-search');

                    let show = true;

                    if (cabangId && rowCabang !== cabangId) show = false;
                    if (jenjangVal && rowJenjang !== jenjangVal) show = false;
                    if (kelasId && rowKelas !== kelasId) show = false;
                    if (searchTerm && !rowSearch.includes(searchTerm)) show = false;

                    row.style.display = show ? '' : 'none';
                });
            }

            // Cascading: Cabang → Jenjang → Kelas
            filterCabang.addEventListener('change', function () {
                const selectedCabang = this.value;

                // Reset jenjang & kelas
                filterJenjang.value = '';
                filterKelas.value = '';

                if (selectedCabang) {
                    // Enable jenjang, filter its options by cabang
                    filterJenjang.disabled = false;
                    const kelasOptions = filterKelas.querySelectorAll('option');
                    // Collect unique jenjang values for this cabang
                    const availableJenjang = new Set();
                    kelasOptions.forEach(option => {
                        if (option.value === '') return;
                        if (option.getAttribute('data-cabang') === selectedCabang) {
                            availableJenjang.add(option.getAttribute('data-jenjang'));
                        }
                    });

                    // Show/hide jenjang options
                    const jenjangOptions = filterJenjang.querySelectorAll('option');
                    jenjangOptions.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        option.style.display = availableJenjang.has(option.value) ? '' : 'none';
                    });

                    // Show all kelas for this cabang
                    filterKelas.disabled = false;
                    kelasOptions.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        const kelasCabang = option.getAttribute('data-cabang');
                        option.style.display = (kelasCabang === selectedCabang) ? '' : 'none';
                    });
                } else {
                    // Disable jenjang & kelas
                    filterJenjang.disabled = true;
                    filterKelas.disabled = true;
                }

                filterSiswa();
            });

            // Jenjang change → filter kelas options
            filterJenjang.addEventListener('change', function () {
                const selectedCabang = filterCabang.value;
                const selectedJenjang = this.value;

                // Reset kelas
                filterKelas.value = '';

                if (selectedCabang) {
                    filterKelas.disabled = false;
                    const kelasOptions = filterKelas.querySelectorAll('option');
                    kelasOptions.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        const kelasCabang = option.getAttribute('data-cabang');
                        const kelasJenjang = option.getAttribute('data-jenjang');
                        let show = kelasCabang === selectedCabang;
                        if (selectedJenjang) show = show && kelasJenjang === selectedJenjang;
                        option.style.display = show ? '' : 'none';
                    });
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

        // Show/hide Select All row on mobile
        function updateSelectAllRowVisibility() {
            const selectAllRow = document.querySelector('.select-all-row');
            if (window.innerWidth <= 768) {
                selectAllRow.classList.add('mobile-visible');
            } else {
                selectAllRow.classList.remove('mobile-visible');
            }
        }

        // Sync Select All checkboxes
        function syncSelectAllCheckboxes(checked) {
            const allSelectAll = document.querySelectorAll('#selectAll');
            allSelectAll.forEach(cb => cb.checked = checked);
        }

        // Event listeners for Select All row
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectAllRowVisibility();
            
            const selectAllCheckboxes = document.querySelectorAll('#selectAll');
            selectAllCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    syncSelectAllCheckboxes(this.checked);
                    toggleSelectAll();
                });
            });
        });

        window.addEventListener('resize', updateSelectAllRowVisibility);

        function submitForm() {
            const checked = document.querySelectorAll('.siswa-checkbox:checked:not(#selectAll)');

            if (checked.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum ada siswa!',
                    text: 'Silakan pilih minimal satu siswa dari daftar.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#696cff',
                    customClass: {
                        confirmButton: 'btn btn-primary btn-confirm-swal'
                    },
                    buttonsStyling: false
                });
                return;
            }

            const jenisTagihan = document.getElementById('jenis_tagihan').value;
            const jumlah = document.getElementById('jumlah').value;

            if (!jenisTagihan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jenis Tagihan Kosong',
                    text: 'Silakan isi jenis tagihan terlebih dahulu.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#696cff',
                    customClass: {
                        confirmButton: 'btn btn-primary btn-confirm-swal'
                    },
                    buttonsStyling: false
                });
                return;
            }

            if (!jumlah || jumlah === '0') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nominal Kosong',
                    text: 'Silakan isi jumlah tagihan dengan benar.',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#696cff',
                    customClass: {
                        confirmButton: 'btn btn-primary btn-confirm-swal'
                    },
                    buttonsStyling: false
                });
                return;
            }

            // Custom SweetAlert Confirmation
            Swal.fire({
                title: 'Konfirmasi Tagihan',
                html: `<div class="text-center mt-3">
                           <p class="mb-2">Anda akan membuat tagihan "<strong>${jenisTagihan}</strong>"</p>
                           <p class="mb-2">dengan nominal <strong class="text-primary fs-5">Rp ${jumlah}</strong></p>
                           <p class="mb-3">untuk <strong class="text-success">${checked.length}</strong> siswa.</p>
                           <div class="alert alert-warning py-2 mb-0 mt-4" style="font-size: 0.85rem;">
                               <i class="fas fa-exclamation-triangle me-1"></i> Proses ini akan langsung tersimpan.
                           </div>
                       </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save me-1"></i> Ya, Simpan',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                confirmButtonColor: '#696cff',
                cancelButtonColor: '#8592a3',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const jumlahInput = document.getElementById('jumlah');
                    jumlahInput.value = jumlahInput.value.replace(/\./g, '') || '0';
                    document.getElementById('customTagihanForm').submit();
                }
            });
            // confirmModal code removed as we use SweetAlert now
        }
    </script>
@endsection