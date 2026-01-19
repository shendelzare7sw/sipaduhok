@extends('layouts.sneat')

@section('title', 'Buat Tagihan Massal')
@section('page-title', 'Buat Tagihan Massal')
@section('page-subtitle', 'Buat tagihan untuk seluruh siswa dalam satu kelas')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
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
                        <strong>Informasi:</strong>
                        <p class="mb-0 mt-2">
                            Fitur ini akan membuat tagihan untuk <strong>seluruh siswa aktif</strong> dalam kelas yang
                            dipilih.
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

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Kelas <span class="text-danger">*</span></label>
                                <select name="kelas_id" class="form-control border-start border-primary border-3 shadow-sm"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }}) -
                                            {{ $kelas->cabang->nama_cabang ?? 'Cabang tidak diketahui' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_jatuh_tempo"
                                    class="form-control border-start border-primary border-3 shadow-sm"
                                    value="{{ old('tanggal_jatuh_tempo', now()->addMonth()->format('Y-m-d')) }}" required>
                                @error('tanggal_jatuh_tempo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
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
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">Rp</span>
                                            <input type="text" name="tagihan[{{ $key }}]" class="form-control currency-input"
                                                value="{{ old('tagihan.' . $key, '0') }}" placeholder="0">
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
                                <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong></li>
                            <li>Proses ini tidak dapat dibatalkan. Jika terjadi kesalahan, Anda harus mengedit tagihan satu
                                per satu.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Confirm Modal for Remove Field -->
    <div class="modal fade" id="confirmRemoveFieldModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="bx bx-error me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bx bx-error text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <p class="text-center mb-0">Yakin ingin menghapus field tagihan ini?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmRemoveFieldBtn">
                        <i class="bx bx-check me-1"></i> Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let customFieldCounter = 0;
        let fieldToRemove = null;

        function addTagihanField() {
            customFieldCounter++;
            const container = document.getElementById('tagihan-fields-container');

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
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="text"
                                   name="custom_tagihan[${customFieldCounter}]"
                                   class="form-control currency-input"
                                   placeholder="0">
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
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmRemoveFieldModal'));
            confirmModal.show();
        }

        // Handle form submission
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');

            form.addEventListener('submit', function (e) {
                // Parse all currency inputs
                const currencyInputs = form.querySelectorAll('.currency-input');
                currencyInputs.forEach(input => {
                    const rawValue = input.value.replace(/\./g, '');
                    input.value = rawValue || '0';
                });
            });

            // Confirm remove field button handler
            document.getElementById('confirmRemoveFieldBtn').addEventListener('click', function () {
                if (fieldToRemove) {
                    fieldToRemove.remove();
                    fieldToRemove = null;
                }
                bootstrap.Modal.getInstance(document.getElementById('confirmRemoveFieldModal')).hide();
            });
        });
    </script>
@endsection