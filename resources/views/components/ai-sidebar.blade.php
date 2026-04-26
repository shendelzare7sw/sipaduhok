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
        <button type="button" class="btn-close btn-close-white" onclick="closeAiSidebar()" title="Tutup sidebar"></button>
    </div>

    {{-- Sidebar Body (Scrollable) --}}
    <div class="ai-sidebar-body">
        {{-- Provider Info Badge --}}
        @php
            $currentProvider = \App\Models\AppSetting::where('key', 'ai_provider')->first()?->value ?? 'groq';
            $providerName = $currentProvider === 'groq' ? 'Groq Cloud' : 'Google Gemini';
            $providerIcon = $currentProvider === 'groq' ? 'fa-bolt' : 'fa-google';
            $providerColor = $currentProvider === 'groq' ? 'primary' : 'success';
        @endphp
        <div class="alert alert-{{$providerColor}} alert-dismissible fade show mb-3" role="alert" style="padding: 0.75rem 1rem;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas {{$providerIcon}} me-2"></i>
                    <strong>Provider AI:</strong> <span class="fw-bold">{{ $providerName }}</span>
                </div>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.ai-settings.index') }}" class="btn btn-sm btn-outline-{{$providerColor}}" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" title="Ganti Provider">
                    <i class="fas fa-cog me-1"></i> Ubah
                </a>
                @endif
            </div>
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
                       class="form-control"
                       value="{{ $subjectName }}"
                       readonly
                       style="background-color: #f8f9fa;">
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
<div id="aiSidebarBackdrop" class="ai-sidebar-backdrop" onclick="closeAiSidebar()"></div>

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

<style>
/* Sidebar Container */
.ai-sidebar {
    position: fixed;
    top: 0;
    right: -450px; /* Hidden by default */
    width: 450px; /* Default width - can be resized */
    min-width: 350px;
    max-width: 800px;
    height: 100vh;
    height: 100dvh; /* Dynamic viewport height - accounts for mobile browser chrome */
    background: white;
    box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
    z-index: 1050;
    transition: right 0.3s ease-in-out;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.ai-sidebar.active {
    right: 0; /* Slide in */
}

.ai-sidebar.resizing {
    transition: none; /* Disable transition during resize */
}

/* Resize Handle */
.ai-sidebar-resize-handle {
    position: absolute;
    top: 0;
    left: 0;
    width: 8px;
    height: 100%;
    cursor: ew-resize;
    z-index: 11;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-sidebar-resize-handle:hover {
    background: rgba(23, 162, 184, 0.1);
}

.ai-sidebar-resize-handle:hover .resize-indicator {
    background: #17a2b8;
}

.resize-indicator {
    width: 3px;
    height: 40px;
    background: rgba(23, 162, 184, 0.3);
    border-radius: 2px;
    transition: background 0.2s ease;
}

/* Backdrop */
.ai-sidebar-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
}

.ai-sidebar-backdrop.active {
    opacity: 1;
    visibility: visible;
}

/* Header */
.ai-sidebar-header {
    position: sticky;
    top: 0;
    flex-shrink: 0; /* Don't shrink in flex container */
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    z-index: 10;
}

.ai-sidebar-header h5 {
    font-size: 1.1rem;
}

.ai-sidebar-header small {
    font-size: 0.8rem;
}

.ai-sidebar-header .btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.9;
}

.ai-sidebar-header .btn-close-white:hover {
    opacity: 1;
}

/* Body */
.ai-sidebar-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1; /* Fill remaining height after sticky header */
    min-height: 0; /* Required for flex overflow-y: auto to work */
    /* Bottom padding to prevent content hiding behind mobile browser nav bar */
    padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 1.5rem));
}

/* Question Card in Sidebar */
.ai-sidebar .question-card {
    border-left: 4px solid #17a2b8;
    transition: all 0.2s ease;
    margin-bottom: 1rem;
}

.ai-sidebar .question-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.ai-sidebar .question-card input[type="checkbox"]:checked ~ .card {
    border-left-color: #28a745;
    background-color: #f0fff4;
}

/* Responsive */
@media (max-width: 768px) {
    .ai-sidebar {
        width: 100% !important; /* Full width on mobile - override custom width */
        min-width: 100%;
        max-width: 100%;
        right: -100% !important;
        height: 100vh;
        height: 100dvh; /* Dynamic viewport height for mobile browsers */
    }

    .ai-sidebar.active {
        right: 0 !important;
    }

    .ai-sidebar-resize-handle {
        display: none; /* Hide resize handle on mobile */
    }

    /* Extra bottom padding on mobile to avoid browser nav bar overlap */
    .ai-sidebar-body {
        padding-bottom: max(80px, env(safe-area-inset-bottom, 80px));
    }
}

/* Smooth scrollbar */
.ai-sidebar::-webkit-scrollbar {
    width: 8px;
}

.ai-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.ai-sidebar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.ai-sidebar::-webkit-scrollbar-thumb:hover {
    background: #555;
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

// ==============================
// SIDEBAR RESIZE FUNCTIONALITY
// ==============================

(function() {
    const sidebar = document.getElementById('aiQuestionSidebar');
    const resizeHandle = document.getElementById('aiSidebarResizeHandle');

    if (!sidebar || !resizeHandle) return;

    let isResizing = false;
    let startX = 0;
    let startWidth = 0;

    // Load saved width from localStorage
    const savedWidth = localStorage.getItem('aiSidebarWidth');
    if (savedWidth) {
        const width = parseInt(savedWidth);
        // Validate width is within bounds
        if (width >= 350 && width <= 800) {
            sidebar.style.width = width + 'px';
            // Update hidden position as well
            sidebar.style.right = '-' + width + 'px';
        } else {
            // Invalid width, clear localStorage
            localStorage.removeItem('aiSidebarWidth');
        }
    }

    // Start resize
    resizeHandle.addEventListener('mousedown', function(e) {
        isResizing = true;
        startX = e.clientX;
        startWidth = sidebar.offsetWidth;
        sidebar.classList.add('resizing');

        // Prevent text selection during drag
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'ew-resize';

        e.preventDefault();
    });

    // Perform resize
    document.addEventListener('mousemove', function(e) {
        if (!isResizing) return;

        // Calculate new width (drag left = larger, drag right = smaller)
        const deltaX = startX - e.clientX;
        let newWidth = startWidth + deltaX;

        // Enforce min/max constraints
        const minWidth = 350;
        const maxWidth = 800;
        newWidth = Math.max(minWidth, Math.min(maxWidth, newWidth));

        // Apply new width
        sidebar.style.width = newWidth + 'px';

        // If sidebar is active (visible), keep it at right: 0
        // If sidebar is hidden, update the hidden position
        if (!sidebar.classList.contains('active')) {
            sidebar.style.right = '-' + newWidth + 'px';
        }
    });

    // End resize
    document.addEventListener('mouseup', function() {
        if (isResizing) {
            isResizing = false;
            sidebar.classList.remove('resizing');

            // Restore cursor and text selection
            document.body.style.userSelect = '';
            document.body.style.cursor = '';

            // Save width to localStorage
            const currentWidth = sidebar.offsetWidth;
            localStorage.setItem('aiSidebarWidth', currentWidth);

            // Update hidden position for next open
            if (!sidebar.classList.contains('active')) {
                sidebar.style.right = '-' + currentWidth + 'px';
            }
        }
    });

    // CRITICAL FIX: Wait for ai-question-generator.js to load before overriding
    function initializeResizeOverrides() {
        // Check if window.openAiSidebar is defined (from ai-question-generator.js)
        if (typeof window.openAiSidebar !== 'function') {
            // Not loaded yet, retry after 50ms
            setTimeout(initializeResizeOverrides, 50);
            return;
        }

        // Store original functions
        const originalOpen = window.openAiSidebar;
        const originalClose = window.closeAiSidebar;

        // Override openAiSidebar to respect custom width
        window.openAiSidebar = function() {
            // Ensure sidebar respects saved width BEFORE opening
            const savedWidth = localStorage.getItem('aiSidebarWidth');
            if (savedWidth) {
                sidebar.style.width = savedWidth + 'px';
            }

            // CRITICAL FIX: Reset inline right style to allow CSS transition to work
            sidebar.style.right = '';

            // Call original open function to add 'active' class
            if (originalOpen) originalOpen();
        };

        // Override closeAiSidebar - DO NOT manipulate right position during close
        window.closeAiSidebar = function() {
            // Call original close function first (removes 'active' class, triggers CSS transition)
            if (originalClose) originalClose();

            // After closing animation completes, update hidden position for next open
            setTimeout(() => {
                const currentWidth = sidebar.offsetWidth;
                sidebar.style.right = '-' + currentWidth + 'px';
            }, 300); // Wait for CSS transition to finish (0.3s)
        };
    }

    // Start initialization (will retry until ai-question-generator.js loads)
    initializeResizeOverrides();
})();
</script>
