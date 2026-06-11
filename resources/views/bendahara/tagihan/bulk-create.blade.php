@extends('layouts.sneat')

@section('title', 'Buat Tagihan Massal')
@section('page-title', 'Buat Tagihan Massal')
@section('page-subtitle', 'Buat tagihan untuk seluruh siswa dalam satu atau lebih kelas')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/bendahara/tagihan/bulk-create.css'])
@endsection

@section('content')
    <div class="bulk-create-page">
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
                        <strong>Informasi:</strong>
                        <p class="mb-0 mt-2">
                            Fitur ini akan membuat tagihan untuk <strong>seluruh siswa aktif</strong> dalam kelas-kelas yang
                            dipilih (bisa lebih dari satu kelas).
                            Jika siswa sudah memiliki tagihan dengan jenis yang sama, maka tagihan tersebut akan
                            <strong>diperbarui</strong>.
                        </p>
                        <p class="mb-0 mt-2">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            Untuk tagihan <strong>SPP Bulanan</strong>, gunakan fitur <a
                                href="{{ route('bendahara.tagihan.generate-spp') }}" class="alert-link fw-bold">"Generate
                                SPP"</a> yang lebih akurat.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-plus-circle me-2"></i>Form Tagihan Massal
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('bendahara.tagihan.bulk-create') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Kelas <span class="text-danger">*</span></label>
                                <div class="dropdown filter-dropdown">
                                    <button class="btn btn-outline-primary w-100 text-start d-flex justify-content-between align-items-center"
                                            type="button"
                                            id="kelasDropdown"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            data-bs-auto-close="outside">
                                        <span id="kelasDropdownLabel">
                                            <i class="fas fa-school me-2"></i>Pilih Kelas (0 dipilih)
                                        </span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu shadow-lg border-0 w-100" aria-labelledby="kelasDropdown">
                                        <!-- Filter Section -->
                                        <div class="filter-section">
                                            <h6 class="small fw-bold text-primary mb-2">
                                                <i class="fas fa-filter me-1"></i>Filter Kelas
                                            </h6>

                                            <!-- Filter Cabang -->
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold mb-1">Cabang</label>
                                                <select id="filterCabang" class="form-select form-select-sm">
                                                    <option value="">Semua Cabang</option>
                                                    @foreach($kelasList->unique('cabang_id') as $kelas)
                                                        @if($kelas->cabang)
                                                            <option value="{{ $kelas->cabang_id }}">{{ $kelas->cabang->nama_cabang }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Filter Jenjang -->
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold mb-1">Jenjang</label>
                                                <select id="filterJenjang" class="form-select form-select-sm">
                                                    <option value="">Semua Jenjang</option>
                                                    @foreach($kelasList->unique('jenjang') as $kelas)
                                                        <option value="{{ $kelas->jenjang }}">{{ $kelas->jenjang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="d-flex gap-2 mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" data-clear-kelas-selection>
                                                    <i class="fas fa-times me-1"></i>Clear
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary flex-fill" data-select-all-kelas>
                                                    <i class="fas fa-check-double me-1"></i>Pilih Semua
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Kelas List Section -->
                                        <div class="kelas-list-section" id="kelasListContainer">
                                            @foreach($kelasList as $kelas)
                                                <div class="kelas-checkbox-item"
                                                     data-cabang-id="{{ $kelas->cabang_id }}"
                                                     data-jenjang="{{ $kelas->jenjang }}">
                                                    <label>
                                                        <input type="checkbox"
                                                               name="kelas_ids[]"
                                                               value="{{ $kelas->id }}"
                                                               class="form-check-input kelas-checkbox"
                                                               data-nama="{{ $kelas->nama_kelas }}"
                                                               data-jenjang="{{ $kelas->jenjang }}"
                                                               data-cabang="{{ $kelas->cabang->nama_cabang ?? '' }}">
                                                        <span class="flex-grow-1">
                                                            <strong>{{ $kelas->nama_kelas }}</strong>
                                                            <small class="text-muted d-block">
                                                                {{ $kelas->jenjang }} - {{ $kelas->cabang->nama_cabang ?? 'Cabang tidak diketahui' }}
                                                            </small>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                            <div class="empty-state is-hidden" id="emptyState">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0 small">Tidak ada kelas yang sesuai dengan filter</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Kelas Badges -->
                                <div class="selected-kelas-badges" id="selectedKelasBadges"></div>

                                @error('kelas_ids')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                @error('kelas_ids.*')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jatuh Tempo Global (Opsional)</label>
                                <input type="date" id="globalJatuhTempo"
                                    class="form-control border-start border-success border-3 shadow-sm"
                                    value="{{ old('global_jatuh_tempo', now()->addMonth()->format('Y-m-d')) }}">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Isi field ini untuk mengisi semua tanggal jatuh tempo sekaligus
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-light border-start border-success border-3 mb-4">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb text-warning me-1"></i>
                                <strong>Tips:</strong> Anda bisa mengisi "Jatuh Tempo Global" untuk mengisi semua tanggal sekaligus,
                                atau mengisi tanggal jatuh tempo untuk setiap jenis tagihan secara terpisah.
                            </small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-gray-800">
                                <i class="fas fa-money-bill-wave text-warning me-2"></i>Nominal Tagihan
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-add-tagihan-field data-default-date="{{ now()->addMonth()->format('Y-m-d') }}">
                                <i class="fas fa-plus me-1"></i> Tambah Jenis Tagihan
                            </button>
                        </div>
                        <p class="text-muted mb-3 small">
                            Masukkan nominal untuk setiap jenis tagihan. Kosongkan atau isi 0 jika tidak ingin membuat
                            tagihan jenis tersebut.
                        </p>

                        <div class="row g-3 mb-4" id="tagihan-fields-container">
                            @foreach($jenisTagihan as $key => $label)
                                <div class="col-md-6 col-lg-4 tagihan-field-item" data-type="default">
                                    <div class="p-3 bg-light rounded shadow-sm position-relative">
                                        <label class="form-label fw-bold small mb-2">{{ $label }}</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-white">Rp</span>
                                            <input type="text" name="tagihan[{{ $key }}]" class="form-control currency-input"
                                                value="{{ old('tagihan.' . $key, '0') }}" placeholder="0">
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1">Jatuh Tempo</label>
                                            <input type="date" name="tanggal_jatuh_tempo[{{ $key }}]"
                                                class="form-control form-control-sm jatuh-tempo-input"
                                                value="{{ old('tanggal_jatuh_tempo.' . $key, now()->addMonth()->format('Y-m-d')) }}">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-field-button" data-remove-field>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success shadow-sm fw-bold">
                                <i class="fas fa-check me-1"></i> Buat Tagihan Massal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Warning Alert --}}
            <div class="alert alert-warning border-start border-warning border-4 shadow-sm">
                <div class="d-flex">
                    <i class="fas fa-exclamation-triangle fa-lg me-2 mt-1"></i>
                    <div>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pastikan kelas yang dipilih sudah benar sebelum menyimpan.</li>
                            <li>Tagihan akan dibuat untuk tahun ajaran:
                                <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>
                            </li>
                            <li>Proses ini tidak dapat dibatalkan. Jika terjadi kesalahan, Anda harus mengedit tagihan satu
                                per satu.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Confirm Modal Removed (Replaced by SweetAlert2) --}}

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/bendahara/tagihan/bulk-create.js'])
@endsection
