/**
 * AI Question Bank Generator - Frontend Logic
 * Handles AJAX requests, question display, and selection.
 *
 * Kontrak dengan editor Kelola Soal (manage-soal.js):
 * - memanggil window.addQuestion(soalData) untuk setiap soal terpilih;
 * - membaca #soalAccordion beserta .question-input / .narasi-input;
 * - menyediakan window.openAiSidebar / window.closeAiSidebar.
 */

const escapeHtml = (unsafe) => {
    if (!unsafe) return '';
    return String(unsafe)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

const showToast = (type, message) => {
    const icon = type === 'error' ? 'error' : (type === 'success' ? 'success' : 'warning');

    if (window.Swal) {
        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title: message,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
        return;
    }

    window.alert(message);
};

const getQuestionTypeLabel = (type) => ({
    pilihan_ganda: 'Pilihan Ganda',
    pilihan_ganda_kompleks: 'Pilihan Ganda Kompleks',
    benar_salah: 'Benar / Salah',
    uraian: 'Uraian / Essay',
    isian_singkat: 'Isian Singkat',
}[type] || type);

const renderQuestionDetails = (question, type) => {
    switch (type) {
        case 'pilihan_ganda':
        case 'pilihan_ganda_kompleks': {
            const isKompleks = type === 'pilihan_ganda_kompleks';
            const kunciArr = isKompleks ? String(question.kunci_jawaban).split(',').map((k) => k.trim()) : [question.kunci_jawaban];
            const option = (opt) => `
                <div class="${kunciArr.includes(opt) ? 'font-bold text-emerald-700' : 'text-slate-700'}">
                    ${opt}. ${escapeHtml(question[`pilihan_${opt.toLowerCase()}`])}
                    ${kunciArr.includes(opt) ? '<i class="fa-solid fa-circle-check ml-1" aria-hidden="true"></i>' : ''}
                </div>`;

            return `
                <div class="mt-2 grid gap-1 text-xs sm:grid-cols-2">${['A', 'B', 'C', 'D', 'E'].map(option).join('')}</div>
                <p class="mt-2 text-xs"><strong class="text-emerald-700">Kunci jawaban: ${escapeHtml(question.kunci_jawaban)}</strong>${isKompleks ? ' <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Multi-jawaban</span>' : ''}</p>`;
        }
        case 'benar_salah': {
            const isBenar = ['benar', 'true'].includes(String(question.kunci_jawaban).toLowerCase());
            return `<p class="mt-2 text-xs"><strong class="text-emerald-700">Jawaban: ${isBenar ? 'BENAR' : 'SALAH'}</strong></p>`;
        }
        case 'uraian':
            return `
                ${question.rubrik_penilaian ? `
                    <div class="mt-2 rounded-lg bg-slate-100 p-2 text-xs text-slate-700">
                        <strong><i class="fa-solid fa-clipboard-list mr-1" aria-hidden="true"></i>Rubrik penilaian:</strong>
                        <p class="mt-1 whitespace-pre-wrap">${escapeHtml(question.rubrik_penilaian)}</p>
                    </div>` : ''}
                ${question.contoh_jawaban ? `
                    <details class="mt-2 text-xs">
                        <summary class="cursor-pointer text-slate-500">Lihat contoh jawaban</summary>
                        <div class="mt-1 rounded-lg bg-slate-50 p-2 text-slate-700">${escapeHtml(question.contoh_jawaban)}</div>
                    </details>` : ''}`;
        case 'isian_singkat':
            return `
                <p class="mt-2 text-xs"><strong class="text-emerald-700">Jawaban: ${escapeHtml(question.kunci_jawaban)}</strong></p>
                ${question.alternatif_jawaban && question.alternatif_jawaban.length > 0
                    ? `<p class="mt-1 text-[11px] text-slate-500">Alternatif: ${question.alternatif_jawaban.map(escapeHtml).join(', ')}</p>`
                    : ''}`;
        default:
            return '';
    }
};

const init = () => {
    let generatedQuestions = [];
    let selectedQuestions = new Set();

    const sidebar = document.getElementById('aiQuestionSidebar');
    const form = document.getElementById('aiGeneratorForm');
    const generateBtn = document.getElementById('generateBtn');
    const generatedSection = document.getElementById('generatedQuestionsSection');
    const questionsList = document.getElementById('generatedQuestionsList');
    const addSelectedBtn = document.getElementById('addSelectedBtn');
    const regenerateBtn = document.getElementById('regenerateBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const generatedCountSpan = document.getElementById('generatedCount');

    if (!sidebar || !form) {
        return;
    }

    const generateUrl = sidebar.dataset.generateUrl;
    const generateLabel = generateBtn?.innerHTML || '';
    const addLabel = addSelectedBtn?.innerHTML || '';

    const setLoadingState = (isLoading, message = 'Memproses...') => {
        if (!generateBtn) return;
        generateBtn.disabled = isLoading;
        generateBtn.innerHTML = isLoading
            ? `<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> ${escapeHtml(message)}`
            : generateLabel;
    };

    const updateSelectedCount = () => {
        const counter = document.getElementById('selectedCount') || selectedCountSpan;
        if (counter) counter.textContent = selectedQuestions.size;
        if (addSelectedBtn) addSelectedBtn.disabled = selectedQuestions.size === 0;
    };

    const updateModelStatus = (modelUsed, provider) => {
        const badge = document.getElementById('aiModelStatusBadge');
        const switchInfo = document.getElementById('aiModelSwitchInfo');
        if (!badge) return;

        const modelNames = {
            'openai/gpt-oss-120b': 'GPT OSS 120B',
            'openai/gpt-oss-20b': 'GPT OSS 20B',
            'qwen/qwen3.8-27b': 'Qwen 3.8 27B',
            'gemini-2.5-flash': 'Gemini 2.5 Flash',
            'gemini-3.5-flash-lite': 'Gemini 3.5 Flash Lite',
        };

        const friendlyName = modelNames[modelUsed] || modelUsed;
        badge.innerHTML = `<span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>${escapeHtml(friendlyName)}`;
        badge.title = modelUsed;

        if (!switchInfo) return;

        if (provider === 'gemini') {
            switchInfo.textContent = 'Otomatis beralih dari Groq';
            switchInfo.classList.remove('hidden');
            return;
        }

        const configuredModel = badge.dataset.originalModel || '';
        if (configuredModel && modelUsed !== configuredModel) {
            switchInfo.textContent = `Beralih dari ${modelNames[configuredModel] || configuredModel}`;
            switchInfo.classList.remove('hidden');
        } else {
            switchInfo.classList.add('hidden');
        }
    };

    const createQuestionCard = (question, index, type) => {
        const wrapper = document.createElement('label');
        wrapper.className = 'flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 transition hover:border-indigo-300 has-[:checked]:border-indigo-400 has-[:checked]:ring-2 has-[:checked]:ring-indigo-100';
        wrapper.innerHTML = `
            <input type="checkbox" value="${index}" data-index="${index}" checked class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span class="min-w-0 flex-1 text-sm text-slate-800">
                <span class="flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-extrabold text-slate-900"><span class="mr-1.5 rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] text-white">#${index + 1}</span>${getQuestionTypeLabel(type)}</span>
                    ${question.bobot ? `<span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700">Bobot ${escapeHtml(question.bobot)}</span>` : ''}
                </span>
                ${question.narasi ? `
                    <span class="mt-2 block rounded-lg bg-sky-50 p-2 text-xs text-sky-900">
                        <strong class="mb-1 block"><i class="fa-solid fa-book-open mr-1" aria-hidden="true"></i>Teks bacaan / narasi:</strong>
                        <span class="whitespace-pre-line">${escapeHtml(question.narasi)}</span>
                    </span>` : ''}
                <span class="mt-2 block break-words"><strong>Pertanyaan:</strong><br>${escapeHtml(question.pertanyaan)}</span>
                ${renderQuestionDetails(question, type)}
                ${question.penjelasan ? `<span class="mt-2 block rounded-lg bg-slate-50 p-2 text-[11px] text-slate-600"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i><strong>Penjelasan:</strong> ${escapeHtml(question.penjelasan)}</span>` : ''}
            </span>`;

        wrapper.querySelector('input[type="checkbox"]').addEventListener('change', function onToggle() {
            if (this.checked) selectedQuestions.add(index);
            else selectedQuestions.delete(index);
            updateSelectedCount();
        });

        return wrapper;
    };

    const displayGeneratedQuestions = (questions, questionType) => {
        questionsList.innerHTML = '';
        generatedCountSpan.textContent = questions.length;
        questions.forEach((question, index) => questionsList.appendChild(createQuestionCard(question, index, questionType)));
        generatedSection.classList.remove('hidden');
        generatedSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const handleGenerate = async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const data = {
            topic: formData.get('topic'),
            type: formData.get('type'),
            difficulty: formData.get('difficulty'),
            count: parseInt(formData.get('count'), 10),
            custom_instructions: formData.get('custom_instructions') || null,
            generate_narasi: formData.get('generate_narasi') === 'on',
        };

        if (!String(data.topic || '').trim()) {
            showToast('error', 'Topik tidak boleh kosong!');
            return;
        }

        setLoadingState(true, `Membuat ${data.count} soal...`);

        try {
            const response = await fetch(generateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Gagal generate soal');
            }

            generatedQuestions = result.questions || [];
            displayGeneratedQuestions(generatedQuestions, data.type);

            if (result.metadata) {
                updateModelStatus(result.metadata.model_used, result.metadata.provider);
            }

            let successMsg = result.message || `Berhasil generate ${generatedQuestions.length} soal!`;
            if (result.metadata && result.metadata.provider === 'gemini') {
                successMsg += ' (via Gemini - Groq tidak tersedia)';
            }
            showToast('success', successMsg);

            selectedQuestions = new Set(generatedQuestions.map((_, idx) => idx));
            updateSelectedCount();
        } catch (error) {
            console.error('AI Generation Error:', error);
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
    };

    const addQuestionsToForm = async (questions) => {
        if (typeof window.addQuestion !== 'function') {
            showToast('error', 'Fungsi addQuestion tidak tersedia. Pastikan Anda di halaman Kelola Soal.');
            throw new Error('addQuestion function not found');
        }

        // Hapus soal #1 bawaan yang masih kosong sebelum menambahkan soal AI.
        const accordion = document.getElementById('soalAccordion');
        if (accordion && accordion.children.length === 1) {
            const firstQuestion = accordion.children[0];
            const pertanyaanValue = firstQuestion.querySelector('.question-input')?.value.trim() || '';
            const narasiValue = firstQuestion.querySelector('.narasi-input')?.value.trim() || '';

            if (!pertanyaanValue && !narasiValue) {
                firstQuestion.remove();
            }
        }

        questions.forEach((q) => {
            const soalData = {
                id: '',
                tipe_soal: q.tipe_soal,
                pertanyaan: q.pertanyaan,
                bobot_nilai: q.bobot || 10,
                narasi: q.narasi || null,
                pilihan_jawaban: null,
                kunci_jawaban: null,
            };

            if (q.tipe_soal === 'pilihan_ganda' || q.tipe_soal === 'pilihan_ganda_kompleks') {
                soalData.pilihan_jawaban = {
                    A: q.pilihan_a || '',
                    B: q.pilihan_b || '',
                    C: q.pilihan_c || '',
                    D: q.pilihan_d || '',
                    E: q.pilihan_e || '',
                };

                soalData.kunci_jawaban = q.tipe_soal === 'pilihan_ganda_kompleks'
                    ? String(q.kunci_jawaban || '').split(',').map((k) => k.trim()).filter((k) => k)
                    : q.kunci_jawaban;
            } else if (q.tipe_soal === 'benar_salah') {
                const isBenar = ['benar', 'true', 'b', '1'].includes(String(q.kunci_jawaban).toLowerCase());
                soalData.pilihan_jawaban = { pernyataan: [{ pernyataan: '', kunci: isBenar ? 'B' : 'S' }] };
            } else if (q.tipe_soal === 'isian_singkat') {
                soalData.kunci_jawaban = q.kunci_jawaban;
            }

            window.addQuestion(soalData);
        });

        showToast('success', `${questions.length} soal AI ditambahkan. Tinjau lalu klik "Simpan semua".`);

        setTimeout(() => {
            if (typeof window.closeAiSidebar === 'function') window.closeAiSidebar();
        }, 500);

        setTimeout(() => {
            const list = document.getElementById('soalAccordion');
            const firstQuestion = list?.children[0];
            if (!firstQuestion) return;
            firstQuestion.scrollIntoView({ behavior: 'smooth', block: 'start' });
            list.classList.add('ring-4', 'ring-emerald-300', 'rounded-2xl');
            setTimeout(() => list.classList.remove('ring-4', 'ring-emerald-300', 'rounded-2xl'), 2000);
        }, 800);
    };

    const handleAddSelected = async () => {
        if (selectedQuestions.size === 0) {
            showToast('warning', 'Pilih minimal 1 soal untuk ditambahkan!');
            return;
        }

        const selectedQuestionsArray = Array.from(selectedQuestions).map((idx) => generatedQuestions[idx]).filter((q) => q);

        if (selectedQuestionsArray.length === 0) {
            showToast('error', 'Tidak ada soal valid yang dipilih');
            return;
        }

        addSelectedBtn.disabled = true;
        addSelectedBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Menambahkan...';

        try {
            await addQuestionsToForm(selectedQuestionsArray);
            generatedQuestions = [];
            selectedQuestions.clear();
            generatedSection.classList.add('hidden');
            form.reset();
        } catch (error) {
            console.error('Add Questions Error:', error);
            showToast('error', `Gagal menambahkan soal: ${error.message}`);
        } finally {
            addSelectedBtn.innerHTML = addLabel;
            updateSelectedCount();
        }
    };

    const handleRegenerate = async () => {
        const result = await (window.CleanFlow?.confirmAction
            ? window.CleanFlow.confirmAction({
                title: 'Generate ulang soal?',
                text: 'Semua hasil generate sebelumnya akan digantikan.',
                confirmText: 'Ya, generate ulang',
            })
            : Promise.resolve({ isConfirmed: window.confirm('Generate ulang dan mengganti hasil sebelumnya?') }));

        if (!result.isConfirmed) return;

        generatedSection.classList.add('hidden');
        generatedQuestions = [];
        selectedQuestions.clear();
        form.requestSubmit();
    };

    const openAiSidebar = () => {
        const backdrop = document.getElementById('aiSidebarBackdrop');
        if (!backdrop) return;
        sidebar.classList.add('active');
        backdrop.classList.add('active');
        document.body.classList.add('overflow-hidden');
    };

    const closeAiSidebar = () => {
        const backdrop = document.getElementById('aiSidebarBackdrop');
        if (!backdrop) return;
        sidebar.classList.remove('active');
        backdrop.classList.remove('active');
        document.body.classList.remove('overflow-hidden');
    };

    form.addEventListener('submit', handleGenerate);
    addSelectedBtn?.addEventListener('click', handleAddSelected);
    regenerateBtn?.addEventListener('click', handleRegenerate);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar.classList.contains('active')) {
            window.closeAiSidebar();
        }
    });

    window.openAiSidebar = openAiSidebar;
    window.closeAiSidebar = closeAiSidebar;
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
