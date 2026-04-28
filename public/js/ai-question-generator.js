/**
 * AI Question Bank Generator - Frontend Logic
 * Handles AJAX requests, question display, and selection
 */

(function() {
    'use strict';

    // State management
    let generatedQuestions = [];
    let selectedQuestions = new Set();

    // DOM elements
    const form = document.getElementById('aiGeneratorForm');
    const generateBtn = document.getElementById('generateBtn');
    const generatedSection = document.getElementById('generatedQuestionsSection');
    const questionsList = document.getElementById('generatedQuestionsList');
    const addSelectedBtn = document.getElementById('addSelectedBtn');
    const regenerateBtn = document.getElementById('regenerateBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const generatedCountSpan = document.getElementById('generatedCount');

    // Hidden inputs
    const ujianId = document.getElementById('aiGeneratorUjianId').value;
    const kelasId = document.getElementById('aiGeneratorKelasId').value;
    const mapelId = document.getElementById('aiGeneratorMapelId').value;

    // Initialize
    if (form) {
        form.addEventListener('submit', handleGenerate);
    }

    if (addSelectedBtn) {
        addSelectedBtn.addEventListener('click', handleAddSelected);
    }

    if (regenerateBtn) {
        regenerateBtn.addEventListener('click', handleRegenerate);
    }

    /**
     * Handle Generate Questions
     */
    async function handleGenerate(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {
            topic: formData.get('topic'),
            type: formData.get('type'),
            difficulty: formData.get('difficulty'),
            count: parseInt(formData.get('count')),
            custom_instructions: formData.get('custom_instructions') || null,
            generate_narasi: formData.get('generate_narasi') === 'on' // Checkbox value
        };

        // Validation
        if (!data.topic.trim()) {
            showToast('error', 'Topik tidak boleh kosong!');
            return;
        }

        // Show loading state
        setLoadingState(true, `Generating ${data.count} soal...`);

        try {
            const url = `/guru/lms/${kelasId}/${mapelId}/ujian/${ujianId}/ai-generate-questions`;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Gagal generate soal');
            }

            // Success!
            generatedQuestions = result.questions || [];
            displayGeneratedQuestions(generatedQuestions, data.type);

            // Update model status badge if metadata available
            if (result.metadata) {
                updateModelStatus(result.metadata.model_used, result.metadata.provider);
            }

            // Show metadata info if available
            let successMsg = result.message || `Berhasil generate ${generatedQuestions.length} soal!`;
            if (result.metadata && result.metadata.provider === 'gemini') {
                successMsg += ' (via Gemini - Groq quota exceeded)';
            }
            showToast('success', successMsg);

            // Auto-select all questions
            selectedQuestions = new Set(generatedQuestions.map((_, idx) => idx));
            updateSelectedCount();

        } catch (error) {
            console.error('AI Generation Error:', error);

            // Better error messages
            let errorMsg = error.message || 'Terjadi kesalahan saat generate soal';
            if (errorMsg.includes('timeout')) {
                errorMsg = 'Request timeout - coba kurangi jumlah soal atau periksa koneksi internet';
            } else if (errorMsg.includes('network')) {
                errorMsg = 'Koneksi gagal - periksa koneksi internet Anda';
            }

            showToast('error', errorMsg);
        } finally {
            setLoadingState(false);
        }
    }

    /**
     * Display Generated Questions
     */
    function displayGeneratedQuestions(questions, questionType) {
        questionsList.innerHTML = '';
        generatedCountSpan.textContent = questions.length;

        questions.forEach((question, index) => {
            const card = createQuestionCard(question, index, questionType);
            questionsList.appendChild(card);
        });

        // Show results section
        generatedSection.classList.remove('d-none');

        // Scroll to results
        generatedSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Create Question Card HTML
     */
    function createQuestionCard(question, index, type) {
        const wrapper = document.createElement('div');
        wrapper.className = 'question-card mb-3';

        const cardHtml = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${index}" id="question${index}"
                       data-index="${index}" checked>
                <label class="form-check-label w-100" for="question${index}">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0">
                                    <span class="badge bg-primary me-2">#${index + 1}</span>
                                    ${getQuestionTypeLabel(type)}
                                </h6>
                                ${question.bobot ? `<span class="badge bg-info">Bobot: ${question.bobot}</span>` : ''}
                            </div>

                            ${question.narasi ? `
                                <div class="alert alert-info mb-3">
                                    <div class="fw-bold mb-1"><i class="fas fa-book-open me-1"></i> Teks Bacaan / Narasi:</div>
                                    <div style="white-space: pre-line;">${escapeHtml(question.narasi)}</div>
                                </div>
                            ` : ''}

                            <p class="mb-2"><strong>Pertanyaan:</strong><br>${escapeHtml(question.pertanyaan)}</p>

                            ${renderQuestionDetails(question, type)}

                            ${question.penjelasan ? `
                                <div class="alert alert-light mb-0 mt-2">
                                    <small><i class="fas fa-info-circle me-1"></i> <strong>Penjelasan:</strong> ${escapeHtml(question.penjelasan)}</small>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </label>
            </div>
        `;

        wrapper.innerHTML = cardHtml;

        // Add event listener for checkbox
        const checkbox = wrapper.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedQuestions.add(index);
            } else {
                selectedQuestions.delete(index);
            }
            updateSelectedCount();
        });

        return wrapper;
    }

    /**
     * Render question details based on type
     */
    function renderQuestionDetails(question, type) {
        switch (type) {
            case 'pilihan_ganda':
            case 'pilihan_ganda_kompleks':
                const isKompleks = type === 'pilihan_ganda_kompleks';
                const kunciArr = isKompleks ? String(question.kunci_jawaban).split(',').map(k => k.trim()) : [question.kunci_jawaban];
                return `
                    <div class="ms-3">
                        <div class="row">
                            <div class="col-md-6">
                                ${['A','B','C'].map(opt => `
                                    <div class="${kunciArr.includes(opt) ? 'text-success fw-bold' : ''}">
                                        ${opt}. ${escapeHtml(question['pilihan_' + opt.toLowerCase()])}
                                        ${kunciArr.includes(opt) ? '<i class="fas fa-check-circle ms-1"></i>' : ''}
                                    </div>
                                `).join('')}
                            </div>
                            <div class="col-md-6">
                                ${['D','E'].map(opt => `
                                    <div class="${kunciArr.includes(opt) ? 'text-success fw-bold' : ''}">
                                        ${opt}. ${escapeHtml(question['pilihan_' + opt.toLowerCase()])}
                                        ${kunciArr.includes(opt) ? '<i class="fas fa-check-circle ms-1"></i>' : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <p class="mt-2 mb-0"><strong class="text-success">Kunci Jawaban: ${question.kunci_jawaban}</strong>${isKompleks ? ' <span class="badge bg-warning text-dark ms-1">Multi-jawaban</span>' : ''}</p>
                    </div>
                `;

            case 'benar_salah':
                const isBenar = ['benar', 'true'].includes(String(question.kunci_jawaban).toLowerCase());
                return `
                    <div class="ms-3">
                        <p class="mb-0">
                            <strong class="text-success">Jawaban: ${isBenar ? 'BENAR' : 'SALAH'}</strong>
                        </p>
                    </div>
                `;

            case 'uraian':
                return `
                    <div class="ms-3">
                        ${question.rubrik_penilaian ? `
                            <div class="alert alert-secondary mb-2">
                                <strong><i class="fas fa-clipboard-list me-1"></i> Rubrik Penilaian:</strong>
                                <pre class="mb-0 mt-2" style="white-space: pre-wrap; font-family: inherit; font-size: 0.9rem;">${escapeHtml(question.rubrik_penilaian)}</pre>
                            </div>
                        ` : ''}
                        ${question.contoh_jawaban ? `
                            <details>
                                <summary class="text-muted" style="cursor: pointer;"><small>Lihat contoh jawaban</small></summary>
                                <div class="mt-2 p-2 bg-light rounded">
                                    <small>${escapeHtml(question.contoh_jawaban)}</small>
                                </div>
                            </details>
                        ` : ''}
                    </div>
                `;

            case 'isian_singkat':
                return `
                    <div class="ms-3">
                        <p class="mb-0">
                            <strong class="text-success">Jawaban: ${escapeHtml(question.kunci_jawaban)}</strong>
                        </p>
                        ${question.alternatif_jawaban && question.alternatif_jawaban.length > 0 ? `
                            <p class="mb-0 mt-1">
                                <small class="text-muted">Alternatif: ${question.alternatif_jawaban.map(escapeHtml).join(', ')}</small>
                            </p>
                        ` : ''}
                    </div>
                `;

            default:
                return '';
        }
    }

    /**
     * Handle Add Selected Questions
     */
    async function handleAddSelected() {
        if (selectedQuestions.size === 0) {
            showToast('warning', 'Pilih minimal 1 soal untuk ditambahkan!');
            return;
        }

        const selectedQuestionsArray = Array.from(selectedQuestions)
            .map(idx => generatedQuestions[idx])
            .filter(q => q); // Filter out undefined

        if (selectedQuestionsArray.length === 0) {
            showToast('error', 'Tidak ada soal valid yang dipilih');
            return;
        }

        // Show loading
        addSelectedBtn.disabled = true;
        addSelectedBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menambahkan...';

        try {
            // Add questions to the manual form below
            // This will trigger the existing form fields to be populated
            await addQuestionsToForm(selectedQuestionsArray);

            showToast('success', `Berhasil menambahkan ${selectedQuestionsArray.length} soal! Scroll ke bawah untuk review.`);

            // Reset state
            generatedQuestions = [];
            selectedQuestions.clear();
            generatedSection.classList.add('d-none');
            form.reset();

        } catch (error) {
            console.error('Add Questions Error:', error);
            showToast('error', 'Gagal menambahkan soal: ' + error.message);
        } finally {
            addSelectedBtn.disabled = false;
            addSelectedBtn.innerHTML = '<i class="fas fa-plus-circle me-1"></i> Tambahkan Soal yang Dipilih (<span id="selectedCount">0</span>)';
        }
    }

    /**
     * Add questions to manage-soal accordion
     * Integrates with existing addQuestion() function
     */
    async function addQuestionsToForm(questions) {
        // Check if we're on manage-soal page (has window.addQuestion function)
        if (typeof window.addQuestion !== 'function') {
            showToast('error', 'Fungsi addQuestion tidak tersedia. Pastikan Anda di halaman Kelola Soal.');
            throw new Error('addQuestion function not found');
        }

        try {
            // FIX BUG: Remove empty default question #1 before adding AI questions
            const accordion = document.getElementById('soalAccordion');
            if (accordion && accordion.children.length === 1) {
                const firstQuestion = accordion.children[0];
                const pertanyaanInput = firstQuestion.querySelector('.question-input');
                const narasiInput = firstQuestion.querySelector('.narasi-input');

                // Check if question is truly empty (no pertanyaan, no narasi)
                const pertanyaanValue = pertanyaanInput ? pertanyaanInput.value.trim() : '';
                const narasiValue = narasiInput ? narasiInput.value.trim() : '';

                // If only 1 question exists and it's completely empty, remove it
                // This fixes the bug where auto-generated empty #1 prevents AI questions from being saved
                if (!pertanyaanValue && !narasiValue) {
                    console.log('✅ Removing empty default question #1 before adding AI questions');
                    firstQuestion.remove();
                } else {
                    console.log('ℹ️ Question #1 is not empty, keeping it. AI questions will start from #2.');
                }
            }

            // Add each question to the accordion
            questions.forEach(q => {
                // Convert AI question format to manage-soal format
                const soalData = {
                    id: '', // New question, no ID
                    tipe_soal: q.tipe_soal,
                    pertanyaan: q.pertanyaan,
                    bobot_nilai: q.bobot || 10,
                    narasi: q.narasi || null, // AI can now generate narasi
                    pilihan_jawaban: null,
                    kunci_jawaban: null,
                };

                // Format based on question type
                if (q.tipe_soal === 'pilihan_ganda' || q.tipe_soal === 'pilihan_ganda_kompleks') {
                    soalData.pilihan_jawaban = {
                        A: q.pilihan_a || '',
                        B: q.pilihan_b || '',
                        C: q.pilihan_c || '',
                        D: q.pilihan_d || '',
                        E: q.pilihan_e || '',
                    };

                    if (q.tipe_soal === 'pilihan_ganda_kompleks') {
                        // PGK: kunci_jawaban from AI is a comma-separated string like "A,C,D"
                        // populateSectionData expects an array like ["A","C","D"]
                        let kunciStr = String(q.kunci_jawaban || '');
                        soalData.kunci_jawaban = kunciStr.split(',').map(k => k.trim()).filter(k => k);
                    } else {
                        // PG: kunci_jawaban is a single letter like "A"
                        soalData.kunci_jawaban = q.kunci_jawaban;
                    }
                } else if (q.tipe_soal === 'benar_salah') {
                    // Convert to BS format
                    const isBenar = ['benar', 'true', 'B', '1'].includes(String(q.kunci_jawaban).toLowerCase());
                    soalData.pilihan_jawaban = {
                        pernyataan: [{
                            pernyataan: '',
                            kunci: isBenar ? 'B' : 'S'
                        }]
                    };
                } else if (q.tipe_soal === 'isian_singkat') {
                    soalData.kunci_jawaban = q.kunci_jawaban;
                } else if (q.tipe_soal === 'uraian') {
                    // Uraian doesn't need kunci_jawaban
                }

                // Call existing addQuestion function
                window.addQuestion(soalData);
            });

            // Success toast
            showToast('success', `✅ ${questions.length} soal AI berhasil ditambahkan! Scroll ke bawah untuk review, lalu klik "Simpan Semua" di atas.`);

            // AUTO-CLOSE sidebar after adding questions
            setTimeout(() => {
                if (typeof window.closeAiSidebar === 'function') {
                    window.closeAiSidebar();
                }
            }, 500);

            // Scroll to first new question
            setTimeout(() => {
                const accordion = document.getElementById('soalAccordion');
                if (accordion && accordion.children.length > 0) {
                    // Scroll to the very first question (which should be the first AI question now)
                    const firstQuestion = accordion.children[0];
                    if (firstQuestion) {
                        firstQuestion.scrollIntoView({ behavior: 'smooth', block: 'start' });

                        // Highlight the accordion briefly
                        accordion.style.outline = '3px solid #28a745';
                        setTimeout(() => {
                            accordion.style.outline = 'none';
                        }, 2000);
                    }
                }
            }, 800);

        } catch (error) {
            console.error('Add Questions Error:', error);
            throw error;
        }
    }

    /**
     * Handle Regenerate
     */
    function handleRegenerate() {
        // Show Bootstrap modal instead of native confirm
        const modal = new bootstrap.Modal(document.getElementById('regenerateConfirmModal'));
        modal.show();
    }

    /**
     * Handle Confirmed Regenerate (from modal)
     */
    function handleConfirmedRegenerate() {
        // Hide modal
        const modalEl = document.getElementById('regenerateConfirmModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }

        // Clear generated section
        generatedSection.classList.add('d-none');
        generatedQuestions = [];
        selectedQuestions.clear();

        // Trigger generate again
        form.querySelector('button[type="submit"]').click();
    }

    // Attach event listener to confirm button in modal
    const confirmRegenerateBtn = document.getElementById('confirmRegenerateBtn');
    if (confirmRegenerateBtn) {
        confirmRegenerateBtn.addEventListener('click', handleConfirmedRegenerate);
    }

    /**
     * Update Selected Count
     */
    function updateSelectedCount() {
        const count = selectedQuestions.size;
        selectedCountSpan.textContent = count;

        // Update button state
        addSelectedBtn.disabled = count === 0;
    }

    /**
     * Set Loading State
     */
    function setLoadingState(isLoading, message = 'Loading...') {
        if (isLoading) {
            generateBtn.disabled = true;
            generateBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> ${message}`;
        } else {
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="fas fa-magic me-2"></i> Generate Soal dengan AI';
        }
    }

    /**
     * Show Toast Notification
     */
    function showToast(type, message) {
        // Create toast element
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'warning'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        // Get or create toast container
        let container = document.getElementById('aiToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'aiToastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        // Add toast
        container.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = container.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
        toast.show();

        // Remove from DOM after hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    /**
     * Get Question Type Label
     */
    function getQuestionTypeLabel(type) {
        const labels = {
            pilihan_ganda: 'Pilihan Ganda',
            pilihan_ganda_kompleks: 'Pilihan Ganda Kompleks',
            benar_salah: 'Benar / Salah',
            uraian: 'Uraian / Essay',
            isian_singkat: 'Isian Singkat'
        };
        return labels[type] || type;
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Open AI Sidebar
     */
    function openAiSidebar() {
        const sidebar = document.getElementById('aiQuestionSidebar');
        const backdrop = document.getElementById('aiSidebarBackdrop');

        if (!sidebar || !backdrop) {
            console.error('Sidebar or backdrop element not found');
            return;
        }

        sidebar.classList.add('active');
        backdrop.classList.add('active');

        // Prevent body scroll when sidebar open
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close AI Sidebar
     */
    function closeAiSidebar() {
        const sidebar = document.getElementById('aiQuestionSidebar');
        const backdrop = document.getElementById('aiSidebarBackdrop');

        if (!sidebar || !backdrop) {
            return;
        }

        sidebar.classList.remove('active');
        backdrop.classList.remove('active');

        // Restore body scroll
        document.body.style.overflow = '';
    }

    /**
     * Close sidebar on Escape key
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('aiQuestionSidebar');
            if (sidebar && sidebar.classList.contains('active')) {
                closeAiSidebar();
            }
        }
    });

    /**
     * Update model status badge after generation
     */
    function updateModelStatus(modelUsed, provider) {
        const badge = document.getElementById('aiModelStatusBadge');
        const switchInfo = document.getElementById('aiModelSwitchInfo');
        if (!badge) return;

        // Map model ID to friendly name
        const modelNames = {
            'llama-3.3-70b-versatile': 'Llama 3.3 70B',
            'qwen/qwen3-32b': 'Qwen3 32B',
            'gemini-2.5-flash': 'Gemini 2.5 Flash',
            'meta-llama/llama-4-scout-17b-16e-instruct': 'Llama 4 Scout'
        };

        const friendlyName = modelNames[modelUsed] || modelUsed;
        badge.innerHTML = `<i class="fas fa-circle text-success me-1" style="font-size: 0.5rem;"></i> ${friendlyName}`;
        badge.title = modelUsed;

        // Show switch info if provider changed
        if (provider === 'gemini' && switchInfo) {
            switchInfo.textContent = '⚡ Auto-switched from Groq';
            switchInfo.classList.remove('d-none');
            switchInfo.classList.add('bg-warning', 'text-dark');
        } else if (switchInfo) {
            // Check if model was auto-switched within Groq
            const configuredModel = badge.dataset.originalModel || '';
            if (configuredModel && modelUsed !== configuredModel) {
                switchInfo.textContent = `↔ Switched from ${modelNames[configuredModel] || configuredModel}`;
                switchInfo.classList.remove('d-none');
            } else {
                switchInfo.classList.add('d-none');
            }
        }
    }

    // Make sidebar functions globally accessible
    window.openAiSidebar = openAiSidebar;
    window.closeAiSidebar = closeAiSidebar;

})();
