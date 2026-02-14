@extends('layouts.sneat')

@section('title', 'Buat Tagihan Massal')
@section('page-title', 'Buat Tagihan Massal')
@section('page-subtitle', 'Buat tagihan untuk seluruh siswa dalam satu atau lebih kelas')

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

    /* Filter Dropdown Styles */
    .filter-dropdown .dropdown-menu {
        min-width: 320px;
        max-height: 500px;
        overflow-y: auto;
    }

    .kelas-checkbox-item {
        padding: 8px 12px;
        cursor: pointer;
        transition: background 0.2s;
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .kelas-checkbox-item:hover {
        background: #f8fafc;
    }

    .kelas-checkbox-item label {
        cursor: pointer;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .selected-kelas-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        min-height: 32px;
    }

    .badge-kelas {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-kelas .remove-kelas {
        cursor: pointer;
        color: #0369a1;
        font-weight: bold;
        transition: color 0.2s;
    }

    .badge-kelas .remove-kelas:hover {
        color: #dc2626;
    }

    .filter-section {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .kelas-list-section {
        padding: 12px;
        max-height: 300px;
        overflow-y: auto;
    }

    .empty-state {
        text-align: center;
        padding: 20px;
        color: #94a3b8;
    }
</style>
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
                                                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="clearKelasSelection()">
                                                    <i class="fas fa-times me-1"></i>Clear
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary flex-fill" onclick="selectAllKelas()">
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
                                            <div class="empty-state" id="emptyState" style="display: none;">
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
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTagihanField()">
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
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            onclick="removeTagihanField(this)" style="padding: 2px 8px;">
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
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let customFieldCounter = 0;
        let fieldToRemove = null;

        function addTagihanField() {
            customFieldCounter++;
            const container = document.getElementById('tagihan-fields-container');
            const defaultDate = document.getElementById('globalJatuhTempo').value || '{{ now()->addMonth()->format('Y-m-d') }}';

            const fieldHTML = `
                    <div class="col-md-6 col-lg-4 tagihan-field-item" data-type="custom">
                        <div class="p-3 bg-light rounded shadow-sm position-relative border border-primary">
                            <label class="form-label fw-bold small mb-2">
                                <input type="text"
                                       name="custom_jenis_tagihan[${customFieldCounter}]"
                                       class="form-control form-control-sm mb-2"
                                       placeholder="Nama Jenis Tagihan (contoh: Les Tambahan)"
                                       required>
                            </label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-white">Rp</span>
                                <input type="text"
                                       name="custom_tagihan[${customFieldCounter}]"
                                       class="form-control currency-input"
                                       placeholder="0">
                            </div>
                            <div>
                                <label class="form-label small mb-1">Jatuh Tempo</label>
                                <input type="date"
                                       name="custom_tanggal_jatuh_tempo[${customFieldCounter}]"
                                       class="form-control form-control-sm jatuh-tempo-input"
                                       value="${defaultDate}">
                            </div>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                    onclick="removeTagihanField(this)" style="padding: 2px 8px;">
                                <i class="fas fa-times"></i>
                            </button>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>Jenis tagihan custom
                            </small>
                        </div>
                    </div>
                `;

            container.insertAdjacentHTML('beforeend', fieldHTML);

            // Re-initialize currency formatter for new field
            if (typeof currencyFormatter !== 'undefined' && currencyFormatter.bindInputs) {
                currencyFormatter.bindInputs();
            }
        }

        function removeTagihanField(button) {
            fieldToRemove = button.closest('.tagihan-field-item');

            Swal.fire({
                title: 'Hapus Field?',
                text: "Anda yakin ingin menghapus jenis tagihan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (fieldToRemove) {
                        fieldToRemove.remove();
                        fieldToRemove = null;
                        Swal.fire('Terhapus!', 'Field tagihan telah dihapus.', 'success');
                    }
                }
            });
        }

        // Handle form submission
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');

            form.addEventListener('submit', function (e) {
                // Validate at least one kelas is selected
                const selectedKelas = document.querySelectorAll('.kelas-checkbox:checked');
                if (selectedKelas.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Pilih minimal satu kelas untuk membuat tagihan',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Parse all currency inputs
                const currencyInputs = form.querySelectorAll('.currency-input');
                currencyInputs.forEach(input => {
                    const rawValue = input.value.replace(/\./g, '');
                    input.value = rawValue || '0';
                });
            });

            // Confirm remove field button handler (removed as we use inline onClick/Swal callback)
        });

        // Kelas Selection Management
        const kelasCheckboxes = document.querySelectorAll('.kelas-checkbox');
        const selectedBadgesContainer = document.getElementById('selectedKelasBadges');
        const kelasDropdownLabel = document.getElementById('kelasDropdownLabel');
        const filterCabang = document.getElementById('filterCabang');
        const filterJenjang = document.getElementById('filterJenjang');
        const kelasItems = document.querySelectorAll('.kelas-checkbox-item');
        const emptyState = document.getElementById('emptyState');

        // Update selected kelas display
        function updateSelectedKelas() {
            const selected = Array.from(kelasCheckboxes).filter(cb => cb.checked);
            const count = selected.length;

            // Update dropdown label
            kelasDropdownLabel.innerHTML = `<i class="fas fa-school me-2"></i>Pilih Kelas (${count} dipilih)`;

            // Update badges
            selectedBadgesContainer.innerHTML = '';
            selected.forEach(checkbox => {
                const badge = document.createElement('span');
                badge.className = 'badge-kelas';
                badge.innerHTML = `
                    <span>${checkbox.dataset.nama} (${checkbox.dataset.jenjang})</span>
                    <span class="remove-kelas" onclick="removeKelas(${checkbox.value})">&times;</span>
                `;
                selectedBadgesContainer.appendChild(badge);
            });
        }

        // Remove kelas from selection
        function removeKelas(kelasId) {
            const checkbox = document.querySelector(`.kelas-checkbox[value="${kelasId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedKelas();
            }
        }

        // Filter kelas list
        function filterKelasList() {
            const cabangId = filterCabang.value;
            const jenjang = filterJenjang.value;
            let visibleCount = 0;

            kelasItems.forEach(item => {
                const itemCabangId = item.dataset.cabangId;
                const itemJenjang = item.dataset.jenjang;

                let show = true;

                if (cabangId && itemCabangId !== cabangId) {
                    show = false;
                }

                if (jenjang && itemJenjang !== jenjang) {
                    show = false;
                }

                item.style.display = show ? 'block' : 'none';
                if (show) visibleCount++;
            });

            // Show/hide empty state
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Clear all selections
        function clearKelasSelection() {
            kelasCheckboxes.forEach(cb => cb.checked = false);
            updateSelectedKelas();
        }

        // Select all visible kelas
        function selectAllKelas() {
            kelasItems.forEach(item => {
                if (item.style.display !== 'none') {
                    const checkbox = item.querySelector('.kelas-checkbox');
                    if (checkbox) checkbox.checked = true;
                }
            });
            updateSelectedKelas();
        }

        // Event listeners
        kelasCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedKelas);
        });

        filterCabang.addEventListener('change', filterKelasList);
        filterJenjang.addEventListener('change', filterKelasList);

        // Initialize
        updateSelectedKelas();

        // Global Jatuh Tempo Sync
        const globalJatuhTempo = document.getElementById('globalJatuhTempo');
        if (globalJatuhTempo) {
            globalJatuhTempo.addEventListener('change', function() {
                const value = this.value;
                if (value) {
                    document.querySelectorAll('.jatuh-tempo-input').forEach(input => {
                        input.value = value;
                    });
                }
            });
        }
    </script>
@endsection