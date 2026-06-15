{{--
    AI Question Bank Generator Sidebar Component

    Usage:
    @include('components.ai-sidebar', [
        'ujianId' => $ujian->id,
        'kelasId' => $kelas->id,
        'mapelId' => $mapel->id,
        'subjectName' => $mapel->nama_mapel
    ])
--}}

{{-- Sidebar Container --}}
<div id="aiQuestionSidebar" class="ai-sidebar">
    {{-- Resize Handle --}}
    <div class="ai-sidebar-resize-handle" id="aiSidebarResizeHandle" title="Drag untuk resize sidebar">
        <div class="resize-indicator"></div>
    </div>

    {{-- Sidebar Header --}}
    <div class="ai-sidebar-header">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-robot me-2"></i>
                AI Question Generator
                <span class="badge bg-light text-info ms-2">BETA</span>
            </h5>
            <small class="opacity-90">Generate soal otomatis dengan AI</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-close-ai-sidebar title="Tutup sidebar"></button>
    </div>

    {{-- Sidebar Body (Scrollable) --}}
    <div class="ai-sidebar-body">


        {{-- Model Status Badge --}}
        @php
            $currentModel = \App\Models\AppSetting::where('key', 'ai_model')->first()?->value ?? 'llama-3.3-70b-versatile';
            $modelShortName = str_contains($currentModel, 'llama') ? 'Llama 3.3 70B' :
                              (str_contains($currentModel, 'qwen') ? 'Qwen3 32B' :
                              (str_contains($currentModel, 'gemini') ? 'Gemini 2.5 Flash' : $currentModel));
        @endphp
        <div class="d-flex align-items-center gap-2 mb-3 px-1" id="aiModelStatusContainer">
            <small class="text-muted"><i class="fas fa-microchip me-1"></i> Model:</small>
            <span class="badge bg-dark bg-opacity-75" id="aiModelStatusBadge" title="{{ $currentModel }}">
                <i class="fas fa-circle text-success me-1 ai-sidebar-model-dot"></i>
                {{ $modelShortName }}
            </span>
            <span class="badge bg-light text-muted d-none ai-sidebar-model-switch-info" id="aiModelSwitchInfo">
                {{-- Updated dynamically via JS after generation --}}
            </span>
        </div>

        {{-- Generator Form --}}
        <form id="aiGeneratorForm">
            {{-- Topic Input --}}
            <div class="mb-3">
                <label for="aiTopic" class="form-label fw-bold">
                    <i class="fas fa-book-open text-primary me-1"></i>
                    Topik/Materi Soal <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="aiTopic"
                       name="topic"
                       placeholder="Contoh: Persamaan Kuadrat, Siklus Air, Struktur Teks Berita"
                       required
                       maxlength="200">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Tuliskan topik spesifik untuk hasil terbaik
                </small>
            </div>

            {{-- Subject (Read-only) --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-graduation-cap text-success me-1"></i>
                    Mata Pelajaran
                </label>
                <input type="text"
                       class="form-control ai-sidebar-subject-input"
                       value="{{ $subjectName }}"
                       readonly>
            </div>

            {{-- Question Type --}}
            <div class="mb-3">
                <label for="aiQuestionType" class="form-label fw-bold">
                    <i class="fas fa-list-check text-warning me-1"></i>
                    Tipe Soal <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="aiQuestionType" name="type" required>
                    <option value="pilihan_ganda" selected>Pilihan Ganda (A-E)</option>
                    <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks (multi-jawaban)</option>
                    <option value="benar_salah">Benar / Salah</option>
                    <option value="uraian">Uraian / Essay</option>
                    <option value="isian_singkat">Isian Singkat</option>
                </select>
            </div>

            {{-- Difficulty Level --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-signal text-danger me-1"></i>
                    Tingkat Kesulitan <span class="text-danger">*</span>
                </label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="difficulty" id="difficultyEasy" value="easy">
                    <label class="btn btn-outline-success" for="difficultyEasy">
                        <i class="fas fa-smile"></i> Mudah
                    </label>

                    <input type="radio" class="btn-check" name="difficulty" id="difficultyMedium" value="medium" checked>
                    <label class="btn btn-outline-warning" for="difficultyMedium">
                        <i class="fas fa-meh"></i> Sedang
                    </label>

                    <input type="radio" class="btn-check" name="difficulty" id="difficultyHard" value="hard">
                    <label class="btn btn-outline-danger" for="difficultyHard">
                        <i class="fas fa-frown"></i> Sulit
                    </label>
                </div>
                <small class="text-muted d-block mt-1">
                    <span id="difficultyHint">Aplikasi konsep & perhitungan</span>
                </small>
            </div>

            {{-- Question Count --}}
            <div class="mb-3">
                <label for="aiQuestionCount" class="form-label fw-bold">
                    <i class="fas fa-hashtag text-info me-1"></i>
                    Jumlah Soal <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="aiQuestionCount" name="count" required>
                    <option value="3">3 soal</option>
                    <option value="5" selected>5 soal</option>
                    <option value="7">7 soal</option>
                    <option value="10">10 soal (maksimal)</option>
                </select>
                <small class="text-muted">
                    <i class="fas fa-clock"></i>
                    ~<span id="estimatedTime">15-20</span> detik
                </small>
            </div>

            {{-- Advanced Options (Collapsible) --}}
            <div class="mb-3">
                <a class="text-decoration-none text-muted" data-bs-toggle="collapse" href="#advancedOptions" role="button" aria-expanded="false">
                    <small>
                        <i class="fas fa-cog me-1"></i>
                        <strong>Advanced Options</strong>
                        <i class="fas fa-chevron-down ms-1"></i>
                    </small>
                </a>
                <div class="collapse mt-2" id="advancedOptions">
                    <div class="card card-body bg-light border-0">
                        {{-- Generate Narasi Option --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="generateNarasi" name="generate_narasi">
                            <label class="form-check-label fw-bold" for="generateNarasi">
                                <i class="fas fa-book-reader text-primary me-1"></i>
                                Generate dengan Narasi/Teks Bacaan
                            </label>
                            <div class="text-muted small mt-1">
                                <i class="fas fa-info-circle"></i>
                                AI akan membuat teks bacaan/konteks untuk soal (cocok untuk reading comprehension, analisis teks, dll)
                            </div>
                        </div>

                        <hr class="my-3">

                        <label for="customInstructions" class="form-label">
                            <small><strong>Instruksi Tambahan (Opsional)</strong></small>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="customInstructions"
                                  name="custom_instructions"
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Contoh: fokus pada soal cerita, gunakan konteks kehidupan sehari-hari, hindari perhitungan rumit"></textarea>
                        <small class="text-muted mt-1">
                            <i class="fas fa-info-circle"></i>
                            Berikan instruksi khusus untuk customize hasil generate (max 500 karakter)
                        </small>
                    </div>
                </div>
            </div>

            {{-- Generate Button --}}
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                    <i class="fas fa-magic me-2"></i>
                    Generate Soal dengan AI
                </button>
            </div>

            {{-- Info Box --}}
            <div class="alert alert-info mb-0" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-lightbulb fs-5 me-2 mt-1"></i>
                    <div>
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Gunakan topik yang <strong>spesifik</strong></li>
                            <li>AI menyesuaikan bahasa & complexity sesuai jenjang kelas</li>
                            <li>Review & edit soal sebelum ditambahkan</li>
                            <li>Soal Uraian dilengkapi <strong>rubrik penilaian</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>

        {{-- Generated Questions Result --}}
        <div id="generatedQuestionsSection" class="d-none mt-4">
            <hr class="my-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-check-circle text-success me-2"></i>
                Soal yang Di-Generate (<span id="generatedCount">0</span>)
            </h6>

            <div id="generatedQuestionsList" class="mb-3">
                {{-- Questions will be inserted here via JavaScript --}}
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-secondary" id="regenerateBtn">
                    <i class="fas fa-redo me-1"></i> Regenerate
                </button>
                <button type="button" class="btn btn-success" id="addSelectedBtn">
                    <i class="fas fa-plus-circle me-1"></i>
                    Tambahkan (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Backdrop (click to close) --}}
<div id="aiSidebarBackdrop" class="ai-sidebar-backdrop" data-close-ai-sidebar></div>

{{-- Regenerate Confirmation Modal --}}
<div class="modal fade" id="regenerateConfirmModal" tabindex="-1" aria-labelledby="regenerateConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10 border-bottom border-warning">
                <h5 class="modal-title fw-bold" id="regenerateConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Konfirmasi Regenerate Soal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Perhatian:</strong> Hasil generate sebelumnya akan hilang!
                </div>
                <p class="mb-0">
                    Apakah Anda yakin ingin melakukan regenerate soal?
                    Semua soal yang telah di-generate sebelumnya akan digantikan dengan hasil generate yang baru.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-warning" id="confirmRegenerateBtn">
                    <i class="fas fa-redo me-1"></i> Ya, Regenerate
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden inputs for component data --}}
<input type="hidden" id="aiGeneratorUjianId" value="{{ $ujianId }}">
<input type="hidden" id="aiGeneratorKelasId" value="{{ $kelasId }}">
<input type="hidden" id="aiGeneratorMapelId" value="{{ $mapelId }}">
