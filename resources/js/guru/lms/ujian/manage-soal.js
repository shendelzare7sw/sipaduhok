(() => {
        /**
         * Ambil huruf opsi (A-E) dari sebuah kunci jawaban, apa pun bentuknya.
         * AI bisa mengirim "a", "D. Kebijakan fiskal", atau " c ". Radio/checkbox
         * di form bernilai huruf besar, dan selector CSS case-sensitive - tanpa
         * penyeragaman ini kunci jawaban gagal tercentang lalu tersimpan kosong.
         * Mengembalikan '' kalau tidak ada huruf A-E yang bisa dikenali.
         */
        function normalizeKunciHuruf(nilai) {
            if (nilai === null || nilai === undefined) return '';
            const cocok = String(nilai).match(/[A-Ea-e]/);
            return cocok ? cocok[0].toUpperCase() : '';
        }

// === Global LMS Toast Notification ===
        function showLmsToast(type, message) {
            let container = document.getElementById('lmsToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'lmsToastContainer';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }

            const bgClass = type === 'error' || type === 'danger' ? 'bg-danger' : type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning text-dark' : 'bg-info';
            const icon = type === 'error' || type === 'danger' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            const closeClass = type === 'warning' ? 'btn-close' : 'btn-close btn-close-white';

            const toastHtml = `
                <div class="toast align-items-center text-white ${bgClass} border-0 shadow-lg" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas ${icon} me-2"></i>${message}
                        </div>
                        <button type="button" class="${closeClass} me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', toastHtml);
            const toastEl = container.lastElementChild;
            const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        document.addEventListener('DOMContentLoaded', function () {
            const page = document.querySelector('.guru-lms-ujian-manage-soal-page');

            if (!page) {
                return;
            }

            const loadAiQuestionGenerator = (() => {
                let scriptPromise = null;

                return () => {
                    const src = page.dataset.aiGeneratorSrc;

                    if (!src || window.openAiSidebar) {
                        return Promise.resolve();
                    }

                    if (scriptPromise) {
                        return scriptPromise;
                    }

                    scriptPromise = new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.src = src;
                        script.onload = resolve;
                        script.onerror = () => reject(new Error('AI Question Generator gagal dimuat'));
                        document.body.appendChild(script);
                    });

                    return scriptPromise;
                };
            })();

            loadAiQuestionGenerator().catch(() => showLmsToast('warning', 'AI Question Generator gagal dimuat.'));
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

            // Global counters to prevent index collisions on delete/add
            let questionCounter = 0;
            let bsRowCounters = {};

            // Existing Data
            const existingData = JSON.parse(document.getElementById('soalDataTemplate')?.content?.textContent || '[]');

            // Expose functions globally for onclick handlers
            window.addQuestion = function (data = null) {
                let index = questionCounter++;
                let number = document.querySelectorAll('.soal-item').length + 1;

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
                    previewImg.src = `${page.dataset.storageBaseUrl}/${data.image_path}`;
                    previewContainer.style.display = 'block';
                }

                if (data) {
                    // Set fields
                    el.querySelector('.type-select').value = data.tipe_soal;
                    el.querySelector('input[name="soal[' + index + '][bobot_nilai]"]').value = data.bobot_nilai;

                    // Trigger type change to show correct section
                    changeType(el.querySelector('.type-select'));

                    // Populate Section Data (this will create the option rows from data)
                    populateSectionData(el, index, data);
                } else {
                    // New question: initialize default 5 options for PG and PGK
                    initDefaultPgOptions(el, index, 'pilgan', 5);
                    initDefaultPgOptions(el, index, 'kompleks', 5);
                    // Default 1 BS row if new
                    window.addBsRow(el.querySelector('[data-add-bs-row]'));
                }

                updateTotalBadge();
                updatePreview(el.querySelector('.question-input'));
            };

            let itemToDelete = null;

            window.removeQuestion = function (e, btn) {
                e.stopPropagation(); // Prevent accordion toggle
                
                // Allow direct removal if it's a new question without ID to save clicks
                itemToDelete = btn.closest('.soal-item');
                let idInput = itemToDelete.querySelector('input[name*="[id]"]');
                if (!idInput || !idInput.value) {
                    itemToDelete.remove();
                    renumberQuestions();
                    updateTotalBadge();
                    itemToDelete = null;
                    return;
                }

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
                
                if (typeof bsRowCounters[index] === 'undefined') {
                    bsRowCounters[index] = 0;
                }
                let rowIdx = bsRowCounters[index]++;

                let html = bsTemplate
                    .replace(/{INDEX}/g, index)
                    .replace(/{ROW}/g, rowIdx);

                tbody.insertAdjacentHTML('beforeend', html);
            };

            window.populateSectionData = function (el, index, data) {
                let type = data.tipe_soal;

                if (type === 'pilihan_ganda') {
                    let opts = data.pilihan_jawaban || {};
                    // Filter out non-letter keys like 'jawaban_benar'
                    let optKeys = Object.keys(opts).filter(k => /^[A-E]$/.test(k));
                    let count = Math.max(optKeys.length, 3); // at least 3
                    count = Math.min(count, 5); // at most 5

                    // Initialize option rows
                    initDefaultPgOptions(el, index, 'pilgan', count);

                    // Fill values
                    if (typeof opts === 'object' && opts !== null) {
                        for (let k in opts) {
                            let input = el.querySelector(`input[name="soal[${index}][pilihan_jawaban_pilgan][${k}]"]`);
                            if (input) input.value = opts[k];
                        }
                    }
                    if (data.kunci_jawaban) {
                        // Nilai radio selalu huruf besar A-E. Kunci dari AI kadang
                        // huruf kecil ("d") atau lengkap ("D. Kebijakan fiskal"),
                        // dan selector atribut CSS itu case-sensitive - kalau tidak
                        // diseragamkan, radio tidak pernah tercentang dan kunci
                        // jawaban tersimpan kosong.
                        let huruf = normalizeKunciHuruf(data.kunci_jawaban);
                        if (huruf) {
                            let radio = el.querySelector(`input[name="soal[${index}][kunci_jawaban_pilgan]"][value="${huruf}"]`);
                            if (radio) radio.checked = true;
                        }
                    }
                }
                else if (type === 'pilihan_ganda_kompleks') {
                    let opts = data.pilihan_jawaban || {};
                    let optKeys = Object.keys(opts).filter(k => /^[A-E]$/.test(k));
                    let count = Math.max(optKeys.length, 3);
                    count = Math.min(count, 5);

                    initDefaultPgOptions(el, index, 'kompleks', count);

                    if (typeof opts === 'object' && opts !== null) {
                        for (let k in opts) {
                            let input = el.querySelector(`input[name="soal[${index}][pilihan_jawaban_kompleks][${k}]"]`);
                            if (input) input.value = opts[k];
                        }
                    }
                    let keys = data.kunci_jawaban || [];
                    if (typeof keys === 'string') {
                        try { keys = JSON.parse(keys); } catch(e) { keys = []; }
                    }
                    if (typeof keys === 'string') {
                        keys = keys.split(',');
                    }
                    if (Array.isArray(keys)) {
                        keys.forEach(k => {
                            let huruf = normalizeKunciHuruf(k);
                            if (!huruf) return;
                            let cb = el.querySelector(`input[name="soal[${index}][kunci_jawaban_kompleks][]"][value="${huruf}"]`);
                            if (cb) cb.checked = true;
                        });
                    }
                    // Also init PG defaults for when user switches type
                    initDefaultPgOptions(el, index, 'pilgan', count);
                }
                else if (type === 'benar_salah') {
                    // Init default PG/PGK options for type switching
                    initDefaultPgOptions(el, index, 'pilgan', 5);
                    initDefaultPgOptions(el, index, 'kompleks', 5);

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
                        window.addBsRow(el.querySelector('[data-add-bs-row]'));
                    }
                }
                else if (type === 'isian_singkat' || type === 'uraian') {
                    // Init default PG/PGK options for type switching
                    initDefaultPgOptions(el, index, 'pilgan', 5);
                    initDefaultPgOptions(el, index, 'kompleks', 5);

                    if (type === 'isian_singkat') {
                        let val = data.kunci_jawaban || '';
                        el.querySelector(`input[name="soal[${index}][kunci_jawaban_isian]"]`).value = val;
                    }
                }
            };

            // === DYNAMIC PG OPTION FUNCTIONS ===
            const allLetters = ['A', 'B', 'C', 'D', 'E'];

            /**
             * Create a single PG option row HTML
             */
            function createPgOptionHtml(index, letter, mode) {
                let inputType = mode === 'pilgan' ? 'radio' : 'checkbox';
                let namePrefix = mode === 'pilgan' ? 'pilihan_jawaban_pilgan' : 'pilihan_jawaban_kompleks';
                let keyName = mode === 'pilgan'
                    ? `soal[${index}][kunci_jawaban_pilgan]`
                    : `soal[${index}][kunci_jawaban_kompleks][]`;

                return `<div class="input-group input-group-sm mb-2 pg-option-row" data-letter="${letter}">
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="${inputType}"
                            name="${keyName}" value="${letter}">
                        <span class="ms-2 fw-bold">${letter}</span>
                    </div>
                    <input type="text" name="soal[${index}][${namePrefix}][${letter}]"
                        class="form-control" placeholder="Opsi ${letter}">
                </div>`;
            }

            /**
             * Initialize default PG options for a question
             */
            window.initDefaultPgOptions = function(el, index, mode, count) {
                let containerClass = mode === 'pilgan' ? '.pg-options-container' : '.pgk-options-container';
                let container = el.querySelector(containerClass);
                if (!container) return;

                // Clear existing
                container.innerHTML = '';

                // Add options
                for (let i = 0; i < count; i++) {
                    container.insertAdjacentHTML('beforeend', createPgOptionHtml(index, allLetters[i], mode));
                }
            };

            /**
             * Add a PG/PGK option (max 5)
             */
            window.addPgOption = function(btn, mode) {
                let item = btn.closest('.soal-item');
                let index = item.getAttribute('data-index');
                let containerClass = mode === 'pilgan' ? '.pg-options-container' : '.pgk-options-container';
                let container = item.querySelector(containerClass);
                let currentCount = container.querySelectorAll('.pg-option-row').length;

                if (currentCount >= 5) {
                    showLmsToast('warning', 'Maksimal 5 opsi jawaban (A-E).');
                    return;
                }

                let nextLetter = allLetters[currentCount];
                container.insertAdjacentHTML('beforeend', createPgOptionHtml(index, nextLetter, mode));
            };

            /**
             * Remove last PG/PGK option (min 3)
             */
            window.removePgOption = function(btn, mode) {
                let item = btn.closest('.soal-item');
                let containerClass = mode === 'pilgan' ? '.pg-options-container' : '.pgk-options-container';
                let container = item.querySelector(containerClass);
                let rows = container.querySelectorAll('.pg-option-row');

                if (rows.length <= 3) {
                    showLmsToast('warning', 'Minimal 3 opsi jawaban (A-C).');
                    return;
                }

                // Remove last row
                rows[rows.length - 1].remove();
            };

            page.addEventListener('click', async function(event) {
                const addButton = event.target.closest('[data-add-question]');
                const removeButton = event.target.closest('[data-remove-question]');
                const confirmRemoveButton = event.target.closest('[data-confirm-remove]');
                const syncButton = event.target.closest('[data-sync-action]');
                const aiButton = event.target.closest('[data-open-ai-sidebar]');
                const removeImageButton = event.target.closest('[data-remove-image]');
                const addPgButton = event.target.closest('[data-add-pg-option]');
                const removePgButton = event.target.closest('[data-remove-pg-option]');
                const addBsButton = event.target.closest('[data-add-bs-row]');

                if (addButton) {
                    event.preventDefault();
                    window.addQuestion();
                    return;
                }

                if (removeButton) {
                    event.preventDefault();
                    window.removeQuestion(event, removeButton);
                    return;
                }

                if (confirmRemoveButton) {
                    event.preventDefault();
                    window.confirmRemoveVal();
                    return;
                }

                if (syncButton) {
                    event.preventDefault();
                    window.confirmSyncAction(syncButton.dataset.syncForm, syncButton.dataset.syncTitle, syncButton.dataset.syncMessage);
                    return;
                }

                if (aiButton) {
                    event.preventDefault();
                    try {
                        await loadAiQuestionGenerator();
                        if (window.openAiSidebar) {
                            window.openAiSidebar();
                        }
                    } catch (error) {
                        showLmsToast('warning', 'AI Question Generator gagal dimuat.');
                    }
                    return;
                }

                if (removeImageButton) {
                    event.preventDefault();
                    window.removeImage(removeImageButton.dataset.removeImage);
                    return;
                }

                if (addPgButton) {
                    event.preventDefault();
                    window.addPgOption(addPgButton, addPgButton.dataset.addPgOption);
                    return;
                }

                if (removePgButton) {
                    event.preventDefault();
                    window.removePgOption(removePgButton, removePgButton.dataset.removePgOption);
                    return;
                }

                if (addBsButton) {
                    event.preventDefault();
                    window.addBsRow(addBsButton);
                }
            });

            page.addEventListener('change', function(event) {
                const target = event.target;

                if (target.matches('.type-select')) {
                    window.changeType(target);
                    return;
                }

                if (target.matches('.image-upload')) {
                    window.previewImage(target, target.dataset.previewImage);
                }
            });

            page.addEventListener('input', function(event) {
                if (event.target.matches('.question-input')) {
                    window.updatePreview(event.target);
                }
            });
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
            let relatedCount = Number(page.dataset.relatedCount || 0);

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
                        showLmsToast('error', 'Ukuran gambar terlalu besar! Maksimal 2MB.');
                        input.value = '';
                        return;
                    }

                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        showLmsToast('error', 'Format gambar tidak valid! Gunakan JPG, PNG, atau GIF.');
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
})();
