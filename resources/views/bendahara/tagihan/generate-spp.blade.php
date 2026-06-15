@extends('layouts.sneat')

@section('title', 'Generate SPP Bulanan')
@section('page-title', 'Generate SPP Bulanan')
@section('page-subtitle', 'Buat 12 tagihan SPP otomatis')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection
@section('styles')
    @vite(['resources/css/bendahara/tagihan/generate-spp.css'])
@endsection

@section('content')
    <div class="generate-spp-page">
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
                            <div class="card-header py-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center gap-3 target-card-header">
                                    <h6 class="m-0 fw-bold text-primary generate-target-title">
                                        <i class="fas fa-users me-2"></i>Pilih Target
                                    </h6>
                                    <div class="radio-card-group">
                                        <div class="radio-card">
                                            <input type="radio" name="target_type" id="target_kelas"
                                                value="kelas" checked>
                                            <label class="radio-card-label" for="target_kelas">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title">Per Kelas</span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="radio-card">
                                            <input type="radio" name="target_type" id="target_siswa"
                                                value="siswa">
                                            <label class="radio-card-label" for="target_siswa">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title">Pilih Siswa</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">

                                {{-- Kelas Selection --}}
                                <div id="kelasSelection" class="p-4">
                                    <label class="form-label fw-bold mb-3">Pilih Kelas</label>
                                    
                                    {{-- Filter Section --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">Filter Cabang</label>
                                            <select id="filterCabangClass" class="form-select form-select-sm">
                                                <option value="">Semua Cabang</option>
                                                @foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)
                                                    <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">Filter Jenjang</label>
                                            <select id="filterJenjangClass" class="form-select form-select-sm">
                                                <option value="">Semua Jenjang</option>
                                                @php
                                                    $jenjangList = $kelasList->pluck('jenjang')->unique()->sort();
                                                @endphp
                                                @foreach($jenjangList as $jenjang)
                                                    <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Select All Checkbox --}}
                                    <div class="mb-3 p-2 bg-light rounded">
                                        <label class="form-check-label fw-bold generate-clickable">
                                            <input type="checkbox" id="selectAllClass" class="form-check-input" 
                                                data-select-all-kelas>
                                            <span id="selectAllText">Pilih Semua Kelas</span>
                                        </label>
                                    </div>

                                    {{-- Kelas Checkboxes --}}
                                    <div id="kelasCheckboxContainer" class="kelas-checkbox-container">
                                        @foreach($kelasList as $kelas)
                                            <div class="form-check mb-2 kelas-checkbox-item" 
                                                data-cabang="{{ $kelas->cabang_id }}" 
                                                data-jenjang="{{ $kelas->jenjang }}">
                                                <input class="form-check-input kelas-checkbox" type="checkbox" 
                                                    name="kelas_ids[]" value="{{ $kelas->id }}" 
                                                    id="kelas_{{ $kelas->id }}">
                                                <label class="form-check-label generate-clickable" for="kelas_{{ $kelas->id }}">
                                                    <strong>{{ $kelas->nama_kelas }}</strong> 
                                                    <span class="badge bg-secondary">{{ $kelas->jenjang }}</span>
                                                    <small class="text-muted">{{ $kelas->cabang->nama_cabang ?? '-' }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Selected Count --}}
                                    <small class="text-muted mt-2" id="kelasSelectedCount">0 kelas dipilih</small>
                                </div>

                                {{-- Siswa Selection (Hidden by default) --}}
                                <div id="siswaSelection" class="is-hidden">
                                    {{-- Filter --}}
                                    <div class="p-3 bg-light border-bottom filter-section">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Cabang <span class="text-danger">*</span></label>
                                                <select id="filterCabang" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Cabang --</option>
                                                    @foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)
                                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Jenjang</label>
                                                <select id="filterJenjang" class="form-select form-select-sm" disabled>
                                                    <option value="">-- Pilih Jenjang --</option>
                                                    @php
                                                        $jenjangSiswaList = $kelasList->pluck('jenjang')->unique()->sort();
                                                    @endphp
                                                    @foreach($jenjangSiswaList as $jenjang)
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

                                    {{-- Student Counter --}}
                                    <div
                                        class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center gap-2 spp-counter-section">
                                        <span class="text-muted small">Centang siswa untuk generate SPP</span>
                                        <span class="badge bg-success selected-count-badge" id="selectedCount">0 siswa
                                            dipilih</span>
                                    </div>

                                    {{-- Select All Row (Mobile) --}}
                                    <div class="select-all-row">
                                        <input type="checkbox" id="selectAllMobile" class="siswa-checkbox form-check-input select-all-mobile-checkbox"
                                            data-select-all-siswa>
                                        <label for="selectAllMobile" class="form-check-label">Pilih Semua Siswa</label>
                                    </div>

                                    {{-- Student Table (Responsive) --}}
                                    <div class="table-responsive siswa-table-container">
                                        <table class="table table-hover mb-0 siswa-table">
                                            <thead>
                                                <tr>
                                                    <th width="40" class="text-center">
                                                        <input type="checkbox" id="selectAllTable" class="siswa-checkbox"
                                                            data-select-all-siswa>
                                                    </th>
                                                    <th width="50" class="text-center">NO</th>
                                                    <th class="text-start">NAMA SISWA</th>
                                                    <th class="text-center">NISN</th>
                                                    <th class="text-center">KELAS</th>
                                                    <th class="text-center">CABANG</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($siswaList as $index => $siswa)
                                                    <tr class="siswa-row" data-cabang="{{ $siswa->cabang_id }}"
                                                        data-kelas="{{ $siswa->kelas_id }}"
                                                        data-jenjang="{{ $siswa->kelas->jenjang ?? '' }}"
                                                        data-search="{{ strtolower($siswa->nama_lengkap . ' ' . $siswa->nisn) }}">
                                                        <td class="text-center" data-label="">
                                                            <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}"
                                                                class="siswa-checkbox" data-siswa-checkbox>
                                                        </td>
                                                        <td class="text-center" data-label="NO">{{ $index + 1 }}</td>
                                                        <td data-label="NAMA SISWA"><strong>{{ $siswa->nama_lengkap }}</strong></td>
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
                                    <div class="radio-card-group spp-type-group">
                                        <div class="radio-card">
                                            <input type="radio" name="tipe_spp" id="spp_setahun"
                                                value="setahun" checked>
                                            <label class="radio-card-label" for="spp_setahun">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title"><i class="fas fa-calendar-check text-success me-1"></i>SPP Setahun</span>
                                                    <span class="radio-card-desc">Generate 12 bulan SPP</span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="radio-card">
                                            <input type="radio" name="tipe_spp" id="spp_sebagian"
                                                value="sebagian">
                                            <label class="radio-card-label" for="spp_sebagian">
                                                <span class="radio-card-indicator"></span>
                                                <span class="radio-card-text">
                                                    <span class="radio-card-title"><i class="fas fa-calendar-alt text-warning me-1"></i>SPP Sebagian</span>
                                                    <span class="radio-card-desc">Untuk siswa baru</span>
                                                </span>
                                            </label>
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
                                <div class="mb-3 is-hidden" id="jumlahBulanSection">
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
                                <div class="d-flex gap-2 spp-action-buttons">
                                    <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-secondary flex-fill">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="button" class="btn btn-success flex-fill fw-bold"
                                        data-confirm-generate>
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


@endsection

@section('scripts')
    @vite(['resources/js/bendahara/tagihan/generate-spp.js'])
@endsection
