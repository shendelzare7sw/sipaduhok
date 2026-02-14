@extends('layouts.sneat')

@section('title', 'Generate SPP Bulanan')
@section('page-title', 'Generate SPP Bulanan')
@section('page-subtitle', 'Buat 12 tagihan SPP otomatis')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection
{{-- SweetAlert2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
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
</style>
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
                        <strong>Generate SPP Bulanan</strong>
                        <p class="mb-0 mt-1">Fitur ini akan membuat 12 tagihan SPP bulanan secara otomatis untuk siswa atau
                            kelas yang dipilih. Tagihan yang sudah ada akan di-update dengan nominal baru.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('bendahara.tagihan.generate-spp.store') }}" method="POST" id="generateSppForm">
                @csrf

                <div class="row">
                    {{-- Left Column - Target Selection --}}
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary">
                                    <i class="fas fa-users me-2"></i>Pilih Target
                                </h6>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" id="target_kelas"
                                            value="kelas" checked onchange="toggleTargetType()">
                                        <label class="form-check-label fw-bold" for="target_kelas">Per Kelas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" id="target_siswa"
                                            value="siswa" onchange="toggleTargetType()">
                                        <label class="form-check-label fw-bold" for="target_siswa">Pilih Siswa</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">

                                {{-- Kelas Selection --}}
                                <div id="kelasSelection" class="p-4">
                                    <label class="form-label fw-bold">Pilih Kelas</label>
                                    <select name="target_id" id="kelas_id" class="form-select">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelasList as $kelas)
                                            <option value="{{ $kelas->id }}">
                                                {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }}) -
                                                {{ $kelas->cabang->nama_cabang ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">SPP akan diterapkan ke semua siswa di kelas ini</small>
                                </div>

                                {{-- Siswa Selection (Hidden by default) --}}
                                <div id="siswaSelection" style="display: none;">
                                    {{-- Filter --}}
                                    <div class="p-3 bg-light border-bottom">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <select id="filterCabang" class="form-select form-select-sm">
                                                    <option value="">Semua Cabang</option>
                                                    @foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)
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
                                                <input type="text" id="searchSiswa" class="form-control form-control-sm"
                                                    placeholder="Cari nama/NISN...">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Student Counter --}}
                                    <div
                                        class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Centang siswa yang ingin digenerate SPP-nya</span>
                                        <span class="badge bg-success selected-count-badge" id="selectedCount">0 siswa
                                            dipilih</span>
                                    </div>

                                    {{-- Student Table --}}
                                    <div style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover mb-0 siswa-table">
                                            <thead>
                                                <tr>
                                                    <th width="40">
                                                        <input type="checkbox" id="selectAll" class="siswa-checkbox"
                                                            onclick="toggleSelectAll()">
                                                    </th>
                                                    <th width="50">NO</th>
                                                    <th class="text-start">NAMA SISWA</th>
                                                    <th>NISN</th>
                                                    <th>KELAS</th>
                                                    <th>CABANG</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($siswaList as $index => $siswa)
                                                    <tr class="siswa-row" data-cabang="{{ $siswa->cabang_id }}"
                                                        data-kelas="{{ $siswa->kelas_id }}"
                                                        data-search="{{ strtolower($siswa->nama_lengkap . ' ' . $siswa->nisn) }}">
                                                        <td class="text-center">
                                                            <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}"
                                                                class="siswa-checkbox" onchange="updateSelectedCount()">
                                                        </td>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                                                        <td class="text-center">{{ $siswa->nisn }}</td>
                                                        <td class="text-center">
                                                            <span
                                                                class="badge bg-info">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                                        </td>
                                                        <td class="text-center">
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
                    </div>

                    {{-- Right Column - SPP Settings --}}
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 bg-white">
                                <h6 class="m-0 fw-bold text-success">
                                    <i class="fas fa-calendar-alt me-2"></i>Pengaturan SPP
                                </h6>
                            </div>
                            <div class="card-body">
                                {{-- Tipe SPP --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold mb-3">
                                        Tipe Generate SPP <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipe_spp" id="spp_setahun"
                                                value="setahun" checked onchange="toggleTipeSpp()">
                                            <label class="form-check-label fw-bold" for="spp_setahun">
                                                <i class="fas fa-calendar-check text-success me-1"></i>SPP Setahun
                                            </label>
                                            <div><small class="text-muted">Generate 12 bulan SPP</small></div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipe_spp" id="spp_sebagian"
                                                value="sebagian" onchange="toggleTipeSpp()">
                                            <label class="form-check-label fw-bold" for="spp_sebagian">
                                                <i class="fas fa-calendar-alt text-warning me-1"></i>SPP Sebagian
                                            </label>
                                            <div><small class="text-muted">Untuk siswa baru</small></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bulan Mulai --}}
                                <div class="mb-3">
                                    <label for="bulan_mulai" class="form-label fw-bold">
                                        Bulan Mulai <span class="text-danger">*</span>
                                    </label>
                                    <select name="bulan_mulai" id="bulan_mulai" class="form-select" required>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7" selected>Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                    <small class="text-muted">SPP akan dimulai dari bulan ini</small>
                                </div>

                                {{-- Jumlah Bulan (untuk SPP Sebagian) --}}
                                <div class="mb-3" id="jumlahBulanSection" style="display: none;">
                                    <label for="jumlah_bulan" class="form-label fw-bold">
                                        Jumlah Bulan <span class="text-danger">*</span>
                                    </label>
                                    <select name="jumlah_bulan" id="jumlah_bulan" class="form-select">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i }} Bulan</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted" id="bulanRangeInfo">SPP akan digenerate untuk berapa bulan</small>
                                </div>

                                {{-- Jumlah SPP --}}
                                <div class="mb-3">
                                    <label for="jumlah_spp" class="form-label fw-bold">
                                        Jumlah SPP per Bulan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">Rp</span>
                                        <input type="text" name="jumlah_spp" id="jumlah_spp"
                                            class="form-control currency-input" placeholder="0" required>
                                    </div>
                                </div>

                                {{-- Tanggal Jatuh Tempo --}}
                                <div class="mb-3">
                                    <label for="tanggal_jatuh_tempo" class="form-label fw-bold">
                                        Jatuh Tempo Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <select name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="form-select"
                                        required>
                                        @for($i = 1; $i <= 31; $i++)
                                            <option value="{{ $i }}" {{ $i == 10 ? 'selected' : '' }}>Tanggal {{ $i }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Tanggal jatuh tempo setiap bulan</small>
                                </div>

                                {{-- Preview Info --}}
                                <div class="alert alert-warning mb-3" id="previewInfo">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong>
                                    <ul class="mb-0 mt-2 small" id="previewList">
                                        <li id="previewBulan">Akan dibuat <span id="totalBulanText">12</span> tagihan SPP</li>
                                        <li>Jika tagihan sudah ada, nominal akan diperbarui</li>
                                        <li>Status tagihan baru = "Belum Bayar"</li>
                                    </ul>
                                </div>

                                {{-- Tahun Ajaran --}}
                                <div class="text-muted small mb-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2">
                                    <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-secondary flex-fill">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="button" class="btn btn-success flex-fill fw-bold"
                                        onclick="confirmGenerate()">
                                        <i class="fas fa-calendar-check me-1"></i> Generate SPP
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
    </div>

@endsection

@section('scripts')
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            filterCabang.addEventListener('change', function () {
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

            // Confirm button handler (removed as we use inline onClick/Swal callback)
        });

        function toggleTargetType() {
            const isKelas = document.getElementById('target_kelas').checked;
            document.getElementById('kelasSelection').style.display = isKelas ? 'block' : 'none';
            document.getElementById('siswaSelection').style.display = isKelas ? 'none' : 'block';

            // Update input names
            document.getElementById('kelas_id').name = isKelas ? 'target_id' : 'target_id_kelas';
        }

        function toggleTipeSpp() {
            const isSetahun = document.getElementById('spp_setahun').checked;
            const jumlahBulanSection = document.getElementById('jumlahBulanSection');
            const totalBulanText = document.getElementById('totalBulanText');

            if (isSetahun) {
                jumlahBulanSection.style.display = 'none';
                totalBulanText.textContent = '12';
            } else {
                jumlahBulanSection.style.display = 'block';
                updateJumlahBulan();
            }
        }

        function updateJumlahBulan() {
            const jumlahBulan = document.getElementById('jumlah_bulan').value;
            const bulanMulai = document.getElementById('bulan_mulai').value;
            const totalBulanText = document.getElementById('totalBulanText');
            const bulanRangeInfo = document.getElementById('bulanRangeInfo');

            const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const bulanAkhir = (parseInt(bulanMulai) + parseInt(jumlahBulan) - 1);
            const bulanAkhirIdx = ((bulanAkhir - 1) % 12);

            totalBulanText.textContent = jumlahBulan;
            bulanRangeInfo.innerHTML = `<i class="fas fa-calendar me-1"></i>${namaBulan[parseInt(bulanMulai) - 1]} - ${namaBulan[bulanAkhirIdx]}`;
        }

        // Event listeners for updates
        document.getElementById('jumlah_bulan')?.addEventListener('change', updateJumlahBulan);
        document.getElementById('bulan_mulai')?.addEventListener('change', function() {
            if (!document.getElementById('spp_setahun').checked) {
                updateJumlahBulan();
            }
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
        }

        function confirmGenerate() {
            const targetType = document.querySelector('input[name="target_type"]:checked').value;
            const tipeSpp = document.querySelector('input[name="tipe_spp"]:checked').value;
            const jumlahSpp = document.getElementById('jumlah_spp').value;

            let targetName = '';
            let targetCount = 0;

            if (targetType === 'kelas') {
                const kelasSelect = document.getElementById('kelas_id');
                if (!kelasSelect.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Kelas',
                        text: 'Silakan pilih kelas terlebih dahulu!',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }
                targetName = kelasSelect.options[kelasSelect.selectedIndex].text;
                targetCount = 'seluruh siswa di ' + targetName;
            } else {
                const checked = document.querySelectorAll('.siswa-checkbox:checked:not(#selectAll)');
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum ada siswa',
                        text: 'Silakan pilih minimal satu siswa!',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }
                targetCount = checked.length + ' siswa terpilih';
            }

            if (!jumlahSpp || jumlahSpp === '0') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nominal Kosong',
                    text: 'Silakan isi jumlah SPP per bulan!',
                    confirmButtonColor: '#696cff'
                });
                return;
            }

            const totalBulan = tipeSpp === 'setahun' ? 12 : document.getElementById('jumlah_bulan').value;
            const infoText = tipeSpp === 'setahun'
                ? 'Proses ini akan membuat tagihan untuk satu tahun ajaran penuh (12 bulan).'
                : `Proses ini akan membuat tagihan untuk ${totalBulan} bulan.`;

            Swal.fire({
                title: 'Konfirmasi Generate SPP',
                html: `Anda akan membuat <strong>${totalBulan} tagihan SPP</strong> dengan nominal <strong>Rp ${jumlahSpp}</strong> untuk <strong>${targetCount}</strong>.<br><br><small class="text-muted">${infoText}</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Generate SPP',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const jumlahInput = document.getElementById('jumlah_spp');
                    jumlahInput.value = jumlahInput.value.replace(/\./g, '') || '0';
                    document.getElementById('generateSppForm').submit();
                }
            });
        }
    </script>
@endsection