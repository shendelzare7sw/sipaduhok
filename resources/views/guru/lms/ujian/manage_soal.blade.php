@extends('layouts.lms-guru')

@section('title', 'Kelola Soal: ' . $ujian->judul_ujian)
@section('page-title', 'Kelola Soal')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/ujian/manage-soal.css'])
@endpush

@push('scripts')
    @vite(['resources/js/guru/lms/ujian/manage-soal.js'])
@endpush

@section('content')
    <div class="manage-soal-container guru-lms-ujian-manage-soal-page"
        data-related-count="{{ $relatedUjianCount ?? 0 }}"
        data-storage-base-url="{{ asset('storage') }}"
        data-ai-generator-src="{{ asset('js/ai-question-generator.js') }}?v=1.1">

    {{-- Header & Controls (Outside Form) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 sticky-top bg-white py-3 px-4 border-bottom shadow-sm soal-toolbar-header soal-toolbar-sticky">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.index' : 'guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
                class="btn btn-outline-secondary btn-sm py-1 px-2">
                <i class="fas fa-arrow-left"></i><span class="ms-1 d-none d-sm-inline">Kembali</span>
            </a>
            <h4 class="mb-0 fs-5-mobile">
                Menu Kelola Soal
                <span class="badge bg-primary ms-1" id="totalSoalBadge">0 Soal</span>
            </h4>
        </div>

        <div class="d-flex gap-2 soal-toolbar-actions">
            {{-- Rilis / Tarik Toggle --}}
            <button type="button" class="btn btn-sm {{ $ujian->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                data-sync-action
                data-sync-form="toggleStatusForm"
                data-sync-title="{{ $ujian->is_active ? 'Tarik Kembali ' . (ucfirst($tipeUjian ?? 'ujian')) : 'Rilis ' . (ucfirst($tipeUjian ?? 'ujian')) }}"
                data-sync-message="Mengubah status..."
                title="{{ $ujian->is_active ? 'Klik untuk menyembunyikan dari siswa' : 'Klik untuk menampilkan ke siswa' }}">
                <i class="fas {{ $ujian->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>
                <span class="btn-label-long">{{ $ujian->is_active ? 'Tarik Kembali' : 'Rilis ' . (ucfirst($tipeUjian ?? 'ujian')) }}</span>
                <span class="btn-label-short">{{ $ujian->is_active ? 'Tarik' : 'Rilis' }}</span>
            </button>



            {{-- SIMPAN SEMUA --}}
            <button type="button" class="btn btn-sm btn-primary"
                data-sync-action
                data-sync-form="mainForm"
                data-sync-title="Simpan Semua Soal"
                data-sync-message="Menyimpan perubahan soal..."
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
        @if($aiQuestionGeneratorEnabled)
            <button type="button" class="btn btn-info btn-sm" data-open-ai-sidebar title="Buka AI Question Generator">
                <i class="fas fa-robot me-1"></i>
                <span class="btn-label-long">AI Question Generator</span>
                <span class="btn-label-short">AI</span>
            </button>
        @endif
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

        <div class="text-center py-4 border-2 rounded bg-gradient add-question-card" data-add-question>
            <i class="fas fa-plus-circle me-2 add-question-icon"></i>
            <h5 class="mb-2 add-question-title"><strong>Tambah Soal Baru</strong></h5>
            <small class="text-muted">Klik untuk menambah soal ke nomor selanjutnya</small>
        </div>

    </form>

    {{-- Hidden Form for Toggle --}}
    <form action="{{ route(($tipeUjian ?? 'ujian') === 'latihan' ? 'guru.lms.latihan.toggleStatus' : 'guru.lms.ujian.toggleStatus', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST"
        id="toggleStatusForm" class="d-none">
        @csrf
        <input type="hidden" name="sync_kelas" id="sync_kelas_status" value="0">
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
                    <button type="button" class="btn btn-danger" data-confirm-remove>Hapus</button>
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
            <div class="d-flex align-items-center gap-2 soal-template-header">
                <!-- Delete Button Icon (Samping Dropdown) -->
                <button type="button" class="btn btn-sm btn-outline-danger delete-btn question-delete-btn p-1"
                    data-remove-question title="Hapus Soal">
                    <i class="fas fa-trash"></i>
                </button>

                <!-- Accordion Toggle -->
                <h2 class="accordion-header flex-grow-1 mb-0" id="heading{INDEX}">
                    <button class="accordion-button collapsed d-flex align-items-center soal-accordion-toggle" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse{INDEX}">

                        <!-- Left: Number & Type -->
                        <div class="d-flex align-items-center gap-2 soal-summary">
                            <span class="badge bg-info text-dark fw-bold soal-number-badge">
                                <span class="soal-number">{NUMBER}</span>
                            </span>
                            <span class="badge bg-secondary soal-type-badge">Pilihan Ganda</span>
                            <span class="text-muted small preview-text text-truncate">
                                (Masukan pertanyaan...)
                            </span>
                        </div>
                    </button>
                </h2>
            </div>

            <h2 class="accordion-header d-none" id="headingHidden{INDEX}"></h2>
            <div id="collapse{INDEX}" class="accordion-collapse collapse" data-bs-parent="#soalAccordion">
                <div class="accordion-body bg-light">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe Soal</label>
                            <select name="soal[{INDEX}][tipe_soal]" class="form-select form-select-sm type-select">
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
                            accept="image/png,image/jpeg,image/jpg,image/gif" data-preview-image="{INDEX}">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Upload gambar untuk soal (maks 2MB). Format: JPG, PNG, GIF
                        </small>

                        <!-- Hidden field to store existing image path -->
                        <input type="hidden" name="soal[{INDEX}][existing_image]" class="existing-image-path" value="{IMAGE_PATH}">

                        <!-- Image Preview Area -->
                        <div class="image-preview-container mt-2" id="imagePreview{INDEX}">
                            <div class="card border-info">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <img src="" alt="Preview" class="preview-img img-thumbnail question-preview-img">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-image="{INDEX}" title="Hapus gambar">
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
                            placeholder="Tuliskan pertanyaan..." data-update-preview>{PERTANYAAN}</textarea>
                    </div>

                    <div class="card card-body p-3 bg-white border">
                        <h6 class="card-title small fw-bold mb-3">Opsi Jawaban & Kunci</h6>

                        {{-- 1. PILGAN --}}
                        <div class="type-section section-pilihan_ganda">
                            <div class="pg-options-container"></div>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-xs btn-outline-success" data-add-pg-option="pilgan" title="Tambah Opsi">
                                    <i class="fas fa-plus me-1"></i>Opsi
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger" data-remove-pg-option="pilgan" title="Kurangi Opsi">
                                    <i class="fas fa-minus me-1"></i>Opsi
                                </button>
                                <small class="text-muted align-self-center">(Min 3, Maks 5)</small>
                            </div>
                        </div>

                        {{-- 2. PILGAN KOMPLEKS --}}
                        <div class="type-section section-pilihan_ganda_kompleks">
                            <div class="alert alert-info py-1 px-2 mb-2 scoring-info">
                                <i class="fas fa-info-circle me-1"></i> Penilaian parsial: <strong class="text-success">+Poin</strong> untuk opsi benar, <strong class="text-danger">-Poin</strong> untuk opsi salah (min. 0).
                            </div>
                            <div class="pgk-options-container"></div>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-xs btn-outline-success" data-add-pg-option="kompleks" title="Tambah Opsi">
                                    <i class="fas fa-plus me-1"></i>Opsi
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger" data-remove-pg-option="kompleks" title="Kurangi Opsi">
                                    <i class="fas fa-minus me-1"></i>Opsi
                                </button>
                                <small class="text-muted align-self-center">(Min 3, Maks 5)</small>
                            </div>
                        </div>

                        {{-- 3. BENAR SALAH --}}
                        <div class="type-section section-benar_salah">
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
                            <button type="button" class="btn btn-xs btn-outline-secondary" data-add-bs-row>+
                                Baris</button>
                        </div>

                        {{-- 4. ISIAN --}}
                        <div class="type-section section-isian_singkat">
                            <label class="form-label small">Kunci Jawaban</label>
                            <input type="text" name="soal[{INDEX}][kunci_jawaban_isian]"
                                class="form-control form-control-sm" placeholder="Jawaban singkat...">
                            <small class="text-muted fst-italic isian-help">
                                *AI Assistant tersedia saat koreksi untuk membantu menilai jawaban yang mirip.
                            </small>
                        </div>

                        {{-- 5. URAIAN --}}
                        <div class="type-section section-uraian">
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

    <template id="soalDataTemplate">@json($soalList)</template>

@endsection
