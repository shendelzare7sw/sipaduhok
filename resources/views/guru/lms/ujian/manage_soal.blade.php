@extends('layouts.lms-guru')

@section('title', 'Kelola Soal: ' . $ujian->judul_ujian)
@section('page-title', 'Kelola Soal')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

<style>
    /* Global Improvements for Manage Soal Page */
    .manage-soal-container {
        background: #ffffff;
    }

    .form-label {
        color: #374151;
        font-weight: 500;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #165fac;
        box-shadow: 0 0 0 0.2rem rgba(22, 95, 172, 0.1);
    }

    textarea.question-input {
        border: 1px solid #d1d5db;
        resize: vertical;
    }

    textarea.question-input:focus {
        border-color: #165fac;
        box-shadow: 0 0 0 0.2rem rgba(22, 95, 172, 0.1);
    }

    /* === Mobile Responsive Toolbar === */
    @media (max-width: 767px) {
        /* Header: stack vertically on mobile */
        .soal-toolbar-header {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 10px 12px !important;
            gap: 8px;
        }
        .soal-toolbar-header .soal-toolbar-actions {
            width: 100%;
            flex-wrap: wrap;
            gap: 6px !important;
        }
        .soal-toolbar-header .soal-toolbar-actions .btn {
            flex: 1 1 auto;
            font-size: 12px;
            padding: 5px 8px;
            min-width: 0;
        }
        /* Import/Export toolbar: wrap on mobile */
        .soal-import-toolbar {
            flex-wrap: wrap !important;
            justify-content: flex-start !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
            gap: 6px !important;
        }
        .soal-import-toolbar .btn {
            font-size: 12px;
            padding: 5px 8px;
        }
        /* Hide long text labels on extra-small screens, show short labels */
        .btn-label-long { display: none; }
        .btn-label-short { display: inline !important; }
    }
    @media (min-width: 768px) {
        .btn-label-short { display: none; }
    }
</style>

@section('content')
    <div class="manage-soal-container">

    {{-- Header & Controls (Outside Form) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 sticky-top bg-white py-3 px-4 border-bottom shadow-sm soal-toolbar-header"
        style="z-index: 10;">
        <div>
            <a href="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.index' : 'guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
                class="btn btn-outline-secondary mb-2 btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <h4 class="mb-0">
                Menu Kelola Soal
                <span class="badge bg-primary ms-2" id="totalSoalBadge">0 Soal</span>
            </h4>
        </div>

        <div class="d-flex gap-2 soal-toolbar-actions">
            {{-- Rilis / Tarik Toggle --}}
            <button type="button" class="btn btn-sm {{ $ujian->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                onclick="confirmSyncAction('toggleStatusForm', '{{ $ujian->is_active ? 'Tarik Kembali ' . (ucfirst($tipeUjian ?? 'ujian')) : 'Rilis ' . (ucfirst($tipeUjian ?? 'ujian')) }}', 'Mengubah status...')"
                title="{{ $ujian->is_active ? 'Klik untuk menyembunyikan dari siswa' : 'Klik untuk menampilkan ke siswa' }}">
                <i class="fas {{ $ujian->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>
                <span class="btn-label-long">{{ $ujian->is_active ? 'Tarik Kembali' : 'Rilis ' . (ucfirst($tipeUjian ?? 'ujian')) }}</span>
                <span class="btn-label-short">{{ $ujian->is_active ? 'Tarik' : 'Rilis' }}</span>
            </button>

            {{-- Tampilkan Nilai Toggle --}}
            <button type="button" class="btn btn-sm {{ $ujian->tampilkan_nilai ? 'btn-outline-danger' : 'btn-outline-info' }}"
                onclick="confirmSyncAction('toggleResultForm', '{{ $ujian->tampilkan_nilai ? 'Sembunyikan Nilai' : 'Tampilkan Nilai' }}', 'Mengubah visibilitas nilai...')"
                title="{{ $ujian->tampilkan_nilai ? 'Klik untuk menyembunyikan nilai dari siswa' : 'Klik untuk menampilkan nilai ke siswa' }}">
                <i class="fas {{ $ujian->tampilkan_nilai ? 'fa-eye-slash' : 'fa-poll' }} me-1"></i>
                <span class="btn-label-long">{{ $ujian->tampilkan_nilai ? 'Sembunyikan Nilai' : 'Tampilkan Nilai' }}</span>
                <span class="btn-label-short">Nilai</span>
            </button>

            {{-- SIMPAN SEMUA --}}
            <button type="button" class="btn btn-sm btn-primary" onclick="confirmSyncAction('mainForm', 'Simpan Semua Soal', 'Menyimpan perubahan soal...')"
                title="Simpan semua perubahan soal">
                <i class="fas fa-save me-1"></i>
                <span class="btn-label-long">Simpan Semua</span>
                <span class="btn-label-short">Simpan</span>
            </button>
        </div>
    </div>

    {{-- Import/Export Tools --}}
    <div class="d-flex justify-content-end gap-2 mb-3 px-4 soal-import-toolbar">
        <a href="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.soal.template' : 'guru.lms.ujian.soal.template', [$kelas->id, $mapel->id, $ujian->id]) }}"
            class="btn btn-outline-success btn-sm">
            <i class="fas fa-download me-1"></i>
            <span class="btn-label-long">Download Template {{ ucfirst($tipeUjian ?? 'ujian') }}</span>
            <span class="btn-label-short">Template</span>
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importSoalModal">
            <i class="fas fa-file-import me-1"></i>
            <span class="btn-label-long">Import {{ ucfirst($tipeUjian ?? 'ujian') }} dari Excel</span>
            <span class="btn-label-short">Import</span>
        </button>

        {{-- AI Generator Sidebar Trigger --}}
        <button type="button" class="btn btn-info btn-sm" onclick="openAiSidebar()" title="Buka AI Question Generator">
            <i class="fas fa-robot me-1"></i>
            <span class="btn-label-long">AI Question Generator</span>
            <span class="btn-label-short">AI</span>
        </button>
    </div>

    {{-- MAIN FORM STARTS HERE --}}
    <form action="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.soal.storeAll' : 'guru.lms.ujian.soal.storeAll', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST"
        id="mainForm" enctype="multipart/form-data">
        @csrf

        {{-- Hidden Input for Sync Logic --}}
        <input type="hidden" name="sync_kelas" id="sync_kelas_main" value="0">
        {{-- Pass title for safe lookup --}}
        <input type="hidden" name="original_judul" value="{{ $ujian->judul_ujian }}">

        {{-- Accordion Container --}}
        <div class="accordion mb-4" id="soalAccordion">
            {{-- Items will be injected here via JS --}}
        </div>

        <div class="text-center py-4 border-2 rounded bg-gradient" style="border: 2px dashed #165fac; cursor: pointer; transition: all 0.3s ease; background: linear-gradient(135deg, #f0f9ff 0%, #f8fbff 100%);"
            onclick="addQuestion()" onmouseover="this.style.borderColor='#0d3f7a'; this.style.boxShadow='0 4px 12px rgba(22, 95, 172, 0.15)';"
            onmouseout="this.style.borderColor='#165fac'; this.style.boxShadow='none';">
            <i class="fas fa-plus-circle me-2" style="color: #165fac; font-size: 24px;"></i>
            <h5 class="mb-2" style="color: #165fac;"><strong>Tambah Soal Baru</strong></h5>
            <small class="text-muted">Klik untuk menambah soal ke nomor selanjutnya</small>
        </div>

    </form>

    {{-- Hidden Form for Toggle --}}
    <form action="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.toggleStatus' : 'guru.lms.ujian.toggleStatus', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST"
        id="toggleStatusForm" class="d-none">
        @csrf
        <input type="hidden" name="sync_kelas" id="sync_kelas_status" value="0">
    </form>

    <form action="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.toggleResult' : 'guru.lms.ujian.toggleResult', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST"
        id="toggleResultForm" class="d-none">
        @csrf
        <input type="hidden" name="sync_kelas" id="sync_kelas_result" value="0">
    </form>

    <!-- Sync Confirmation Modal -->
    <div class="modal fade" id="syncConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="syncModalTitle">Konfirmasi Aksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="syncModalMessage">Apakah Anda yakin?</p>
                    
                    @if(isset($relatedUjianCount) && $relatedUjianCount > 0)
                        <div class="alert alert-info py-2 mb-0">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="syncConfirmCheckbox" checked>
                                <label class="form-check-label fw-bold" for="syncConfirmCheckbox">
                                    Terapkan juga ke {{ $relatedUjianCount }} kelas lain?
                                </label>
                            </div>
                            <small class="d-block mt-1 text-muted">
                                Jika dicentang, aksi ini (dan soal-soal) akan diduplikasi ke semua {{ $tipeUjian ?? 'ujian' }} terkait di kelas lain.
                            </small>
                        </div>
                    @else
                        <input type="hidden" id="syncConfirmCheckbox" value="0">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmSync">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteQuestionModal" tabindex="-1" aria-labelledby="deleteQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteQuestionModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus soal ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="confirmRemoveVal()">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Soal Modal -->
    <div class="modal fade" id="importSoalModal" tabindex="-1" aria-labelledby="importSoalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.soal.import' : 'guru.lms.ujian.soal.import', [$kelas->id, $mapel->id, $ujian->id]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importSoalModalLabel">Import Soal {{ ucfirst($tipeUjian ?? 'ujian') }} dari Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">File Excel (.xlsx)</label>
                            <input type="file" name="file_soal" class="form-control" accept=".xlsx,.xls" required>
                        </div>
                        <div class="alert alert-warning small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Soal {{ $tipeUjian ?? 'ujian' }} yang diimport akan <strong>ditambahkan</strong> ke daftar soal yang sudah ada. Download template terlebih dahulu untuk format yang benar.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div> {{-- End manage-soal-container --}}

    {{-- AI Question Generator Sidebar --}}
    @include('components.ai-sidebar', [
        'ujianId' => $ujian->id,
        'kelasId' => $kelas->id,
        'mapelId' => $mapel->id,
        'subjectName' => $mapel->nama_mapel
    ])

    {{-- Template for New Question --}}
    <template id="soalTemplate">
        <div class="accordion-item soal-item" data-index="{INDEX}">
            <input type="hidden" name="soal[{INDEX}][id]" value="{ID}">

            <!-- Header with Delete Button Outside Accordion -->
            <div class="d-flex align-items-center gap-2" style="padding: 8px 15px; background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb;">
                <!-- Delete Button Icon (Samping Dropdown) -->
                <button type="button" class="btn btn-sm btn-outline-danger delete-btn p-1"
                    onclick="removeQuestion(event, this)" title="Hapus Soal" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-trash"></i>
                </button>

                <!-- Accordion Toggle -->
                <h2 class="accordion-header flex-grow-1 mb-0" id="heading{INDEX}">
                    <button class="accordion-button collapsed d-flex align-items-center" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse{INDEX}" style="padding: 8px 0; border: none; background: none;">

                        <!-- Left: Number & Type -->
                        <div class="d-flex align-items-center gap-2" style="min-width: 0; flex: 1;">
                            <span class="badge bg-info text-dark fw-bold" style="min-width: 35px; text-align: center;">
                                <span class="soal-number">{NUMBER}</span>
                            </span>
                            <span class="badge bg-secondary soal-type-badge" style="white-space: nowrap;">Pilihan Ganda</span>
                            <span class="text-muted small preview-text text-truncate" style="max-width: 400px; color: #6c757d !important;">
                                (Masukan pertanyaan...)
                            </span>
                        </div>
                    </button>
                </h2>
            </div>

            <h2 class="accordion-header" id="heading{INDEX}" style="display: none;"></h2>
            <div id="collapse{INDEX}" class="accordion-collapse collapse" data-bs-parent="#soalAccordion">
                <div class="accordion-body bg-light">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe Soal</label>
                            <select name="soal[{INDEX}][tipe_soal]" class="form-select form-select-sm type-select"
                                onchange="changeType(this)">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks</option>
                                <option value="benar_salah">Benar - Salah</option>
                                <option value="isian_singkat">Isian Singkat</option>
                                <option value="uraian">Uraian / Essay</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Bobot Nilai</label>
                            <input type="number" name="soal[{INDEX}][bobot_nilai]" class="form-control form-control-sm"
                                value="10" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Narasi / Teks Bacaan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <textarea name="soal[{INDEX}][narasi]" class="form-control narasi-input" rows="2"
                            placeholder="Masukkan narasi/teks bacaan jika soal berbasis narasi..."></textarea>
                        <small class="text-muted">Soal dengan narasi yang sama akan dikelompokkan saat ujian.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">
                            Gambar Soal <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="file" name="soal[{INDEX}][image]" class="form-control form-control-sm image-upload"
                            accept="image/png,image/jpeg,image/jpg,image/gif" onchange="previewImage(this, {INDEX})">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Upload gambar untuk soal (maks 2MB). Format: JPG, PNG, GIF
                        </small>

                        <!-- Hidden field to store existing image path -->
                        <input type="hidden" name="soal[{INDEX}][existing_image]" class="existing-image-path" value="{IMAGE_PATH}">

                        <!-- Image Preview Area -->
                        <div class="image-preview-container mt-2" id="imagePreview{INDEX}" style="display: none;">
                            <div class="card border-info">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <img src="" alt="Preview" class="preview-img img-thumbnail" style="max-width: 200px; max-height: 150px; object-fit: contain;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage({INDEX})" title="Hapus gambar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">Gambar akan ditampilkan saat siswa mengerjakan soal</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pertanyaan</label>
                        <textarea name="soal[{INDEX}][pertanyaan]" class="form-control question-input" rows="3"
                            placeholder="Tuliskan pertanyaan..." oninput="updatePreview(this)">{PERTANYAAN}</textarea>
                    </div>

                    <div class="card card-body p-3 bg-white border">
                        <h6 class="card-title small fw-bold mb-3">Opsi Jawaban & Kunci</h6>

                        {{-- 1. PILGAN --}}
                        <div class="type-section section-pilihan_ganda">
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-text">
                                        <input class="form-check-input mt-0" type="radio"
                                            name="soal[{INDEX}][kunci_jawaban_pilgan]" value="{{ $opt }}">
                                        <span class="ms-2 fw-bold">{{ $opt }}</span>
                                    </div>
                                    <input type="text" name="soal[{INDEX}][pilihan_jawaban_pilgan][{{ $opt }}]"
                                        class="form-control" placeholder="Opsi {{ $opt }}">
                                </div>
                            @endforeach
                        </div>

                        {{-- 2. PILGAN KOMPLEKS --}}
                        <div class="type-section section-pilihan_ganda_kompleks" style="display:none;">
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-text">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="soal[{INDEX}][kunci_jawaban_kompleks][]" value="{{ $opt }}">
                                        <span class="ms-2 fw-bold">{{ $opt }}</span>
                                    </div>
                                    <input type="text" name="soal[{INDEX}][pilihan_jawaban_kompleks][{{ $opt }}]"
                                        class="form-control" placeholder="Opsi {{ $opt }}">
                                </div>
                            @endforeach
                        </div>

                        {{-- 3. BENAR SALAH --}}
                        <div class="type-section section-benar_salah" style="display:none;">
                            <table class="table table-sm table-bordered mb-2">
                                <thead>
                                    <tr>
                                        <th>Pernyataan</th>
                                        <th width="100">Kunci</th>
                                    </tr>
                                </thead>
                                <tbody class="bs-tbody">
                                    {{-- Rows injected by JS for existing data, or default 1 row --}}
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="addBsRow(this)">+
                                Baris</button>
                        </div>

                        {{-- 4. ISIAN --}}
                        <div class="type-section section-isian_singkat" style="display:none;">
                            <label class="form-label small">Kunci Jawaban</label>
                            <input type="text" name="soal[{INDEX}][kunci_jawaban_isian]"
                                class="form-control form-control-sm" placeholder="Jawaban singkat...">
                            <small class="text-muted fst-italic" style="font-size: 0.75rem;">
                                *AI Assistant tersedia saat koreksi untuk membantu menilai jawaban yang mirip.
                            </small>
                        </div>

                        {{-- 5. URAIAN --}}
                        <div class="type-section section-uraian" style="display:none;">
                            <div class="alert alert-info py-2 mb-0 small">Soal uraian dikoreksi manual.</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- BS Row Template --}}
    <template id="bsRowTemplate">
        <tr>
            <td><input type="text" name="soal[{INDEX}][pilihan_jawaban_bs][{ROW}][pernyataan]"
                    class="form-control form-control-sm" placeholder="Pernyataan..."></td>
            <td>
                <select name="soal[{INDEX}][pilihan_jawaban_bs][{ROW}][kunci]" class="form-select form-select-sm">
                    <option value="B">Benar</option>
                    <option value="S">Salah</option>
                </select>
            </td>
        </tr>
    </template>

@endsection

    @push('scripts')
    {{-- AI Question Generator JavaScript --}}
    <script src="{{ asset('js/ai-question-generator.js') }}?v=1.0"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Variables initialized after DOM load
            const container = document.getElementById('soalAccordion');
            let templateEl = document.getElementById('soalTemplate');
            let bsTemplateEl = document.getElementById('bsRowTemplate');

            if (!container || !templateEl || !bsTemplateEl) {
                console.error("Templates or Container not found!");
                return;
            }

            const template = templateEl.innerHTML;
            const bsTemplate = bsTemplateEl.innerHTML;

            // Existing Data
            const existingData = @json($soalList);

            // Expose functions globally for onclick handlers
            window.addQuestion = function (data = null) {
                let index = document.querySelectorAll('.soal-item').length;
                let number = index + 1;

                let contentRaw = data ? (data.pertanyaan || '') : '';

                let html = template
                    .replace(/{INDEX}/g, index)
                    .replace(/{NUMBER}/g, number)
                    .replace(/{ID}/g, data ? data.id : '')
                    .replace(/{PERTANYAAN}/g, '') // We set value via JS to be safe
                    .replace(/{IMAGE_PATH}/g, data && data.image_path ? data.image_path : '');

                // Insert HTML
                container.insertAdjacentHTML('beforeend', html);

                // Get the newly added element
                let el = container.lastElementChild;

                // Set Pertanyaan safely
                el.querySelector('.question-input').value = contentRaw;

                // Set Narasi if exists
                if (data && data.narasi) {
                    el.querySelector('.narasi-input').value = data.narasi;
                }

                // Set Image Preview if exists
                if (data && data.image_path) {
                    const previewContainer = el.querySelector('#imagePreview' + index);
                    const previewImg = previewContainer.querySelector('.preview-img');
                    previewImg.src = '{{ asset('storage') }}/' + data.image_path;
                    previewContainer.style.display = 'block';
                }

                if (data) {
                    // Set fields
                    el.querySelector('.type-select').value = data.tipe_soal;
                    el.querySelector('input[name="soal[' + index + '][bobot_nilai]"]').value = data.bobot_nilai;

                    // Trigger type change to show correct section
                    changeType(el.querySelector('.type-select'));

                    // Populate Section Data
                    populateSectionData(el, index, data);
                } else {
                    // Default 1 BS row if new
                    addBsRow(el.querySelector('button[onclick="addBsRow(this)"]'));
                }

                updateTotalBadge();
                updatePreview(el.querySelector('.question-input'));
            };

            let itemToDelete = null;

            window.removeQuestion = function (e, btn) {
                e.stopPropagation(); // Prevent accordion toggle
                itemToDelete = btn.closest('.soal-item');
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteQuestionModal'));
                deleteModal.show();
            };

            window.confirmRemoveVal = function() {
                if (itemToDelete) {
                    itemToDelete.remove();
                    renumberQuestions();
                    updateTotalBadge();
                    itemToDelete = null;
                }
                var modalEl = document.getElementById('deleteQuestionModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            };

            window.renumberQuestions = function () {
                let items = document.querySelectorAll('.soal-item');
                items.forEach((item, idx) => {
                    let newNum = idx + 1;
                    item.querySelector('.soal-number').textContent = newNum;
                });
            };

            window.changeType = function (select) {
                let item = select.closest('.soal-item');
                let type = select.value;

                // Update Badge
                let badge = item.querySelector('.soal-type-badge');
                badge.textContent = select.options[select.selectedIndex].text;

                // Show/Hide Sections
                item.querySelectorAll('.type-section').forEach(el => el.style.display = 'none');
                item.querySelector('.section-' + type).style.display = 'block';
            };

            window.updatePreview = function (textarea) {
                let val = textarea.value;
                let item = textarea.closest('.soal-item');
                let preview = item.querySelector('.preview-text');
                preview.textContent = val ? '(' + val.substring(0, 40) + '...)' : '(Masukan pertanyaan...)';
            };

            window.updateTotalBadge = function () {
                let count = document.querySelectorAll('.soal-item').length;
                document.getElementById('totalSoalBadge').textContent = count + ' Soal';
            };

            window.addBsRow = function (btn) {
                let tbody = btn.previousElementSibling.querySelector('tbody');
                let item = btn.closest('.soal-item');
                let index = item.getAttribute('data-index');
                let rowIdx = tbody.children.length;

                let html = bsTemplate
                    .replace(/{INDEX}/g, index)
                    .replace(/{ROW}/g, rowIdx);

                tbody.insertAdjacentHTML('beforeend', html);
            };

            window.populateSectionData = function (el, index, data) {
                let type = data.tipe_soal;

                if (type === 'pilihan_ganda') {
                    let opts = data.pilihan_jawaban || {};
                    if (typeof opts === 'object' && opts !== null) {
                        for (let k in opts) {
                            let input = el.querySelector(`input[name="soal[${index}][pilihan_jawaban_pilgan][${k}]"]`);
                            if (input) input.value = opts[k];
                        }
                    }
                    if (data.kunci_jawaban) {
                        let radio = el.querySelector(`input[name="soal[${index}][kunci_jawaban_pilgan]"][value="${data.kunci_jawaban}"]`);
                        if (radio) radio.checked = true;
                    }
                }
                else if (type === 'pilihan_ganda_kompleks') {
                    let opts = data.pilihan_jawaban || {};
                    if (typeof opts === 'object' && opts !== null) {
                        for (let k in opts) {
                            let input = el.querySelector(`input[name="soal[${index}][pilihan_jawaban_kompleks][${k}]"]`);
                            if (input) input.value = opts[k];
                        }
                    }
                    let keys = data.kunci_jawaban || [];
                    if (Array.isArray(keys)) {
                        keys.forEach(k => {
                            let cb = el.querySelector(`input[name="soal[${index}][kunci_jawaban_kompleks][]"][value="${k}"]`);
                            if (cb) cb.checked = true;
                        });
                    }
                }
                else if (type === 'benar_salah') {
                    let rows = [];
                    if (data.pilihan_jawaban && data.pilihan_jawaban.pernyataan) {
                        rows = data.pilihan_jawaban.pernyataan;
                    }

                    let tbody = el.querySelector('.bs-tbody');
                    if (rows.length > 0) {
                        rows.forEach((row, rIdx) => {
                            let text = row.pernyataan || row.text || '';
                            let isTrue = row.benar === true || row.kunci === 'B';
                            let keyChar = isTrue ? 'B' : 'S';

                            let html = bsTemplate
                                .replace(/{INDEX}/g, index)
                                .replace(/{ROW}/g, rIdx);
                            tbody.insertAdjacentHTML('beforeend', html);

                            let rowEl = tbody.lastElementChild;
                            rowEl.querySelector('input').value = text;
                            rowEl.querySelector('select').value = keyChar;
                        });
                    } else {
                        window.addBsRow(el.querySelector('button[onclick="addBsRow(this)"]'));
                    }
                }
                else if (type === 'isian_singkat') {
                    let val = data.kunci_jawaban || '';
                    el.querySelector(`input[name="soal[${index}][kunci_jawaban_isian]"]`).value = val;
                }
            };

            // Initialize
            if (existingData && existingData.length > 0) {
                existingData.forEach((soal, idx) => {
                    window.addQuestion(soal);
                });
            } else {
                window.addQuestion();
            }

            // --- SYNC ACTIONS LOGIC ---
            let targetFormId = null;
            let relatedCount = {{ $relatedUjianCount ?? 0 }};

            window.confirmSyncAction = function(formId, title, message) {
                targetFormId = formId;
                
                // If no related classes, just submit directly
                if (relatedCount === 0) {
                    document.getElementById(formId).submit();
                    return;
                }

                // Show Modal
                document.getElementById('syncModalTitle').textContent = title;
                document.getElementById('syncModalMessage').textContent = message || "Lanjutkan aksi ini?";
                
                // Reset checkbox default to true
                let cb = document.getElementById('syncConfirmCheckbox');
                if(cb) cb.checked = true;

                var syncModal = new bootstrap.Modal(document.getElementById('syncConfirmModal'));
                syncModal.show();
            };

            document.getElementById('btnConfirmSync').addEventListener('click', function() {
                if (!targetFormId) return;

                let form = document.getElementById(targetFormId);
                let cb = document.getElementById('syncConfirmCheckbox');
                let shouldSync = cb && cb.checked ? 1 : 0;

                // Find the specific hidden input for this form
                let inputName = '';
                if (targetFormId === 'mainForm') inputName = 'sync_kelas_main';
                else if (targetFormId === 'toggleStatusForm') inputName = 'sync_kelas_status';
                else if (targetFormId === 'toggleResultForm') inputName = 'sync_kelas_result';
                
                let input = document.getElementById(inputName);
                if (input) input.value = shouldSync;

                // Submit
                form.submit();
                
                // Close modal
                var modalEl = document.getElementById('syncConfirmModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            });

            // --- IMAGE HANDLING FUNCTIONS ---

            /**
             * Preview image when file is selected
             */
            window.previewImage = function(input, index) {
                const previewContainer = document.getElementById('imagePreview' + index);
                const previewImg = previewContainer.querySelector('.preview-img');

                if (input.files && input.files[0]) {
                    const file = input.files[0];

                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran gambar terlalu besar! Maksimal 2MB.');
                        input.value = '';
                        return;
                    }

                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert('Format gambar tidak valid! Gunakan JPG, PNG, atau GIF.');
                        input.value = '';
                        return;
                    }

                    // Read and preview image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                }
            };

            /**
             * Remove image preview and clear file input
             */
            window.removeImage = function(index) {
                const soalItem = document.querySelector(`.soal-item[data-index="${index}"]`);
                if (!soalItem) return;

                const fileInput = soalItem.querySelector('.image-upload');
                const existingImageInput = soalItem.querySelector('.existing-image-path');
                const previewContainer = document.getElementById('imagePreview' + index);

                // Clear file input
                if (fileInput) fileInput.value = '';

                // Clear existing image path (to delete on save)
                if (existingImageInput) existingImageInput.value = '';

                // Hide preview
                if (previewContainer) previewContainer.style.display = 'none';
            };

        });
    </script>
    <style>
        /* Accordion Items Styling */
        .accordion-item {
            border: 1px solid #e5e7eb;
            margin-bottom: 8px;
            border-radius: 6px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .accordion-item:hover {
            border-color: #165fac;
            box-shadow: 0 2px 8px rgba(22, 95, 172, 0.1);
        }

        .soal-item .accordion-button {
            padding: 0 !important;
            background-color: transparent;
            border: none;
            font-size: 0.95rem;
        }

        .soal-item .accordion-button:not(.collapsed) {
            color: #165fac;
        }

        .soal-item .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-body {
            padding: 20px;
        }

        /* Delete Button Styling */
        .delete-btn {
            transition: all 0.2s ease;
            color: #6c757d;
            border-color: #dee2e6;
        }

        .delete-btn:hover {
            background-color: #fee2e2 !important;
            border-color: #dc2626 !important;
            color: #dc2626 !important;
        }

        /* Preview text color */
        .preview-text {
            color: #6c757d !important;
            font-style: italic;
        }

        /* Badge styling */
        .accordion-header .badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .accordion-header {
                flex-wrap: wrap;
            }

            .delete-btn {
                margin-bottom: 8px;
            }
        }
    </style>
@endpush
