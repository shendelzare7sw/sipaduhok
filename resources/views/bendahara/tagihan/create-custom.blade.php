@extends('layouts.app')

@section('title', 'Tambah Tagihan Custom')
@section('page-title', 'Tambah Tagihan Custom')
@section('page-subtitle', 'Input tagihan khusus untuk beberapa siswa sekaligus')


@section('styles')
    @vite(['resources/css/bendahara/tagihan/create-custom.css'])
@endsection

@section('content')
    <div class="create-custom-page">
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
                        <p class="mb-0 mt-1">Pilih beberapa siswa menggunakan checkbox, lalu tentukan jenis tagihan dan
                            nominalnya. Tagihan akan diterapkan ke semua siswa yang dipilih.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('bendahara.tagihan.store-custom') }}" method="POST" id="customTagihanForm">
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
                                    <input type="checkbox" id="selectAllMobile" class="siswa-checkbox form-check-input student-select-all">
                                    <label for="selectAllMobile" class="form-check-label">Pilih Semua Siswa</label>
                                </div>

                                <div class="table-responsive siswa-table-scroll">
                                    <table class="table table-hover mb-0 siswa-table">
                                        <thead>
                                            <tr>
                                                <th width="40" class="text-center">
                                                    <input type="checkbox" id="selectAllTable" class="siswa-checkbox student-select-all">
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
                                                            class="siswa-checkbox student-checkbox">
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
                                        value="{{ old('tanggal_jatuh_tempo', $defaultDueDate) }}"
                                        min="{{ $tagihanDateMin }}" max="{{ $tagihanDateMax }}"
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
                                    <a href="{{ route('bendahara.tagihan.index') }}"
                                        class="btn btn-secondary flex-fill">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="button" class="btn btn-primary flex-fill fw-bold" data-submit-custom-tagihan>
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
    @vite(['resources/js/bendahara/tagihan/create-custom.js'])
@endsection
