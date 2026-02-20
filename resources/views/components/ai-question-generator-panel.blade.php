{{--
    AI Question Bank Generator Panel Component

    Usage:
    @include('components.ai-question-generator-panel', [
        'ujianId' => $ujian->id,
        'kelasId' => $kelas->id,
        'mapelId' => $mapel->id,
        'subjectName' => $mapel->nama_mapel
    ])
--}}

<div class="card shadow-sm mb-4 border-start border-info border-4" id="aiQuestionGeneratorPanel">
    <div class="card-header bg-gradient-info text-white" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#aiGeneratorCollapse">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-robot me-2"></i>
                    AI Question Bank Generator
                    <span class="badge bg-light text-info ms-2">BETA</span>
                </h5>
                <small class="opacity-90">Generate soal otomatis dengan AI - cocok untuk semua jenjang (SD, SMP, SMA)</small>
            </div>
            <i class="fas fa-chevron-down transition" id="collapseIcon"></i>
        </div>
    </div>

    <div class="collapse" id="aiGeneratorCollapse">
        <div class="card-body">
            {{-- Generator Form --}}
            <form id="aiGeneratorForm">
                <div class="row">
                    <div class="col-md-8">
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
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-graduation-cap text-success me-1"></i>
                                Mata Pelajaran
                            </label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $subjectName }}"
                                   readonly
                                   style="background-color: #f8f9fa;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="aiQuestionType" class="form-label fw-bold">
                                <i class="fas fa-list-check text-warning me-1"></i>
                                Tipe Soal <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="aiQuestionType" name="type" required>
                                <option value="pilihan_ganda" selected>Pilihan Ganda (A-E)</option>
                                <option value="benar_salah">Benar / Salah</option>
                                <option value="uraian">Uraian / Essay</option>
                                <option value="isian_singkat">Isian Singkat</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
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
                    </div>

                    <div class="col-md-4">
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
                    </div>
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

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                        <i class="fas fa-magic me-2"></i>
                        Generate Soal dengan AI
                    </button>
                </div>

                {{-- Info Box --}}
                <div class="alert alert-info mt-3 mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-lightbulb fs-4 me-3 mt-1"></i>
                        <div>
                            <strong>Tips untuk hasil terbaik:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan topik yang <strong>spesifik</strong> (contoh: "Pythagoras" lebih baik dari "Matematika")</li>
                                <li>AI akan otomatis menyesuaikan <strong>bahasa & complexity</strong> sesuai jenjang kelas</li>
                                <li>Review & edit soal hasil generate sebelum ditambahkan ke ujian</li>
                                <li>Untuk soal Uraian, AI akan generate <strong>rubrik penilaian</strong> otomatis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Generated Questions Result --}}
    <div class="card-footer bg-light d-none" id="generatedQuestionsSection">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-check-circle text-success me-2"></i>
            Soal yang Di-Generate (<span id="generatedCount">0</span>)
        </h6>

        <div id="generatedQuestionsList" class="mb-3">
            {{-- Questions will be inserted here via JavaScript --}}
        </div>

        <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-outline-secondary" id="regenerateBtn">
                <i class="fas fa-redo me-1"></i> Regenerate Semua
            </button>
            <button type="button" class="btn btn-success" id="addSelectedBtn">
                <i class="fas fa-plus-circle me-1"></i>
                Tambahkan Soal yang Dipilih (<span id="selectedCount">0</span>)
            </button>
        </div>
    </div>
</div>

{{-- Hidden inputs for component data --}}
<input type="hidden" id="aiGeneratorUjianId" value="{{ $ujianId }}">
<input type="hidden" id="aiGeneratorKelasId" value="{{ $kelasId }}">
<input type="hidden" id="aiGeneratorMapelId" value="{{ $mapelId }}">

<style>
    #aiQuestionGeneratorPanel .collapse.show ~ .card-header #collapseIcon {
        transform: rotate(180deg);
    }

    .transition {
        transition: all 0.3s ease;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }

    .question-card {
        border-left: 4px solid #17a2b8;
        transition: all 0.2s ease;
    }

    .question-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateX(4px);
    }

    .question-card input[type="checkbox"]:checked ~ .card {
        border-left-color: #28a745;
        background-color: #f0fff4;
    }
</style>

<script>
    // Difficulty hints
    const difficultyHints = {
        easy: 'Fakta dasar & hafalan',
        medium: 'Aplikasi konsep & perhitungan',
        hard: 'Analisis & problem solving'
    };

    // Estimated time based on count
    const estimatedTimes = {
        3: '10-15',
        5: '15-20',
        7: '20-25',
        10: '25-30'
    };

    // Update difficulty hint
    document.querySelectorAll('input[name="difficulty"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('difficultyHint').textContent = difficultyHints[this.value];
        });
    });

    // Update estimated time
    document.getElementById('aiQuestionCount').addEventListener('change', function() {
        document.getElementById('estimatedTime').textContent = estimatedTimes[this.value];
    });

    // Auto-expand on page load (optional, can be removed)
    // document.addEventListener('DOMContentLoaded', function() {
    //     const collapse = new bootstrap.Collapse(document.getElementById('aiGeneratorCollapse'));
    // });
</script>
