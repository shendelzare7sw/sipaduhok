import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import '../../../components/ai-question-generator.js';
import '../../../components/ai-sidebar.js';

/**
 * Editor Kelola Soal (Guru LMS).
 *
 * Tetap berupa modul JS karena AI Question Generator menyuntikkan soal melalui
 * window.addQuestion dan membaca DOM #soalAccordion secara langsung. Tampilan
 * sepenuhnya utility Tailwind; tidak ada lagi ketergantungan Bootstrap
 * (collapse/modal/toast diganti toggle sendiri, <dialog>, dan SweetAlert).
 */
(() => {
    /**
     * Ambil huruf opsi (A-E) dari sebuah kunci jawaban, apa pun bentuknya.
     * AI bisa mengirim "a", "D. Kebijakan fiskal", atau " c ". Radio/checkbox
     * di form bernilai huruf besar, dan selector CSS case-sensitive - tanpa
     * penyeragaman ini kunci jawaban gagal tercentang lalu tersimpan kosong.
     */
    function normalizeKunciHuruf(nilai) {
        if (nilai === null || nilai === undefined) return '';
        const cocok = String(nilai).match(/[A-Ea-e]/);
        return cocok ? cocok[0].toUpperCase() : '';
    }

    function showLmsToast(type, message) {
        const icon = type === 'error' || type === 'danger' ? 'error' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'info'));

        Swal.fire({ toast: true, position: 'top-end', icon, title: message, showConfirmButton: false, timer: 3500, timerProgressBar: true });
    }

    const optionInputClass = 'block h-9 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';

    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.guru-lms-ujian-manage-soal-page');

        if (!page) {
            return;
        }

        const container = document.getElementById('soalAccordion');
        const templateEl = document.getElementById('soalTemplate');
        const bsTemplateEl = document.getElementById('bsRowTemplate');

        if (!container || !templateEl || !bsTemplateEl) {
            console.error('Templates or Container not found!');
            return;
        }

        const template = templateEl.innerHTML;
        const bsTemplate = bsTemplateEl.innerHTML;

        // Penghitung global agar indeks nama field tidak bertabrakan saat soal dihapus/ditambah.
        let questionCounter = 0;
        const bsRowCounters = {};

        const existingData = JSON.parse(document.getElementById('soalDataTemplate')?.content?.textContent || '[]');

        const toggleSoal = (item, open) => {
            const body = item.querySelector('.soal-body');
            const button = item.querySelector('[data-toggle-soal]');
            const shouldOpen = open ?? body.classList.contains('hidden');

            body.classList.toggle('hidden', !shouldOpen);
            button?.setAttribute('aria-expanded', String(shouldOpen));
            item.querySelector('[data-soal-chevron]')?.classList.toggle('rotate-180', shouldOpen);
            item.classList.toggle('ring-2', shouldOpen);
            item.classList.toggle('ring-indigo-200', shouldOpen);
        };

        window.addQuestion = function (data = null) {
            const index = questionCounter++;
            const number = document.querySelectorAll('.soal-item').length + 1;
            const contentRaw = data ? (data.pertanyaan || '') : '';

            const html = template
                .replace(/{INDEX}/g, index)
                .replace(/{NUMBER}/g, number)
                .replace(/{ID}/g, data ? data.id : '')
                .replace(/{PERTANYAAN}/g, '')
                .replace(/{IMAGE_PATH}/g, data && data.image_path ? data.image_path : '');

            container.insertAdjacentHTML('beforeend', html);
            const el = container.lastElementChild;

            el.querySelector('.question-input').value = contentRaw;

            if (data && data.narasi) {
                el.querySelector('.narasi-input').value = data.narasi;
            }

            if (data && data.image_path) {
                const previewContainer = el.querySelector('#imagePreview' + index);
                previewContainer.querySelector('.preview-img').src = `${page.dataset.storageBaseUrl}/${data.image_path}`;
                previewContainer.classList.remove('hidden');
            }

            if (data) {
                el.querySelector('.type-select').value = data.tipe_soal;
                el.querySelector('input[name="soal[' + index + '][bobot_nilai]"]').value = data.bobot_nilai;
                window.changeType(el.querySelector('.type-select'));
                window.populateSectionData(el, index, data);
            } else {
                window.initDefaultPgOptions(el, index, 'pilgan', 5);
                window.initDefaultPgOptions(el, index, 'kompleks', 5);
                window.addBsRow(el.querySelector('[data-add-bs-row]'));
                toggleSoal(el, true);
            }

            window.updateTotalBadge();
            window.updatePreview(el.querySelector('.question-input'));
            // Tinggi textarea narasi/pertanyaan mengikuti isi yang baru diisi (admin.js → textarea-autogrow).
            window.refreshAutogrow?.(el);
        };

        let itemToDelete = null;
        const deleteDialog = document.getElementById('deleteQuestionDialog');

        window.removeQuestion = function (e, btn) {
            e.stopPropagation();
            itemToDelete = btn.closest('.soal-item');

            // Soal baru (belum punya ID) dihapus langsung tanpa konfirmasi.
            const idInput = itemToDelete.querySelector('input[name*="[id]"]');
            if (!idInput || !idInput.value) {
                itemToDelete.remove();
                window.renumberQuestions();
                window.updateTotalBadge();
                itemToDelete = null;
                return;
            }

            deleteDialog?.showModal();
        };

        window.confirmRemoveVal = function () {
            if (itemToDelete) {
                itemToDelete.remove();
                window.renumberQuestions();
                window.updateTotalBadge();
                itemToDelete = null;
            }
            deleteDialog?.close();
        };

        window.renumberQuestions = function () {
            document.querySelectorAll('.soal-item').forEach((item, idx) => {
                item.querySelector('.soal-number').textContent = idx + 1;
            });
        };

        window.changeType = function (select) {
            const item = select.closest('.soal-item');
            item.querySelector('.soal-type-badge').textContent = select.options[select.selectedIndex].text;
            item.querySelectorAll('.type-section').forEach((section) => section.classList.add('hidden'));
            item.querySelector('.section-' + select.value)?.classList.remove('hidden');
        };

        window.updatePreview = function (textarea) {
            const val = textarea.value;
            textarea.closest('.soal-item').querySelector('.preview-text').textContent = val ? '(' + val.substring(0, 40) + '...)' : '(Masukkan pertanyaan...)';
        };

        window.updateTotalBadge = function () {
            document.getElementById('totalSoalBadge').textContent = document.querySelectorAll('.soal-item').length + ' Soal';
        };

        window.addBsRow = function (btn) {
            const tbody = btn.previousElementSibling.querySelector('tbody');
            const index = btn.closest('.soal-item').getAttribute('data-index');

            if (typeof bsRowCounters[index] === 'undefined') {
                bsRowCounters[index] = 0;
            }
            const rowIdx = bsRowCounters[index]++;

            tbody.insertAdjacentHTML('beforeend', bsTemplate.replace(/{INDEX}/g, index).replace(/{ROW}/g, rowIdx));
        };

        window.populateSectionData = function (el, index, data) {
            const type = data.tipe_soal;

            if (type === 'pilihan_ganda') {
                const opts = data.pilihan_jawaban || {};
                const optKeys = Object.keys(opts).filter((k) => /^[A-E]$/.test(k));
                const count = Math.min(Math.max(optKeys.length, 3), 5);

                window.initDefaultPgOptions(el, index, 'pilgan', count);

                if (typeof opts === 'object' && opts !== null) {
                    for (const k in opts) {
                        const input = el.querySelector(`input[name="soal[${index}][pilihan_jawaban_pilgan][${k}]"]`);
                        if (input) input.value = opts[k];
                    }
                }
                if (data.kunci_jawaban) {
                    const huruf = normalizeKunciHuruf(data.kunci_jawaban);
                    if (huruf) {
                        const radio = el.querySelector(`input[name="soal[${index}][kunci_jawaban_pilgan]"][value="${huruf}"]`);
                        if (radio) radio.checked = true;
                    }
                }
            } else if (type === 'pilihan_ganda_kompleks') {
                const opts = data.pilihan_jawaban || {};
                const optKeys = Object.keys(opts).filter((k) => /^[A-E]$/.test(k));
                const count = Math.min(Math.max(optKeys.length, 3), 5);

                window.initDefaultPgOptions(el, index, 'kompleks', count);

                if (typeof opts === 'object' && opts !== null) {
                    for (const k in opts) {
                        const input = el.querySelector(`input[name="soal[${index}][pilihan_jawaban_kompleks][${k}]"]`);
                        if (input) input.value = opts[k];
                    }
                }
                let keys = data.kunci_jawaban || [];
                if (typeof keys === 'string') {
                    try { keys = JSON.parse(keys); } catch (e) { keys = []; }
                }
                if (typeof keys === 'string') {
                    keys = keys.split(',');
                }
                if (Array.isArray(keys)) {
                    keys.forEach((k) => {
                        const huruf = normalizeKunciHuruf(k);
                        if (!huruf) return;
                        const cb = el.querySelector(`input[name="soal[${index}][kunci_jawaban_kompleks][]"][value="${huruf}"]`);
                        if (cb) cb.checked = true;
                    });
                }
                window.initDefaultPgOptions(el, index, 'pilgan', count);
            } else if (type === 'benar_salah') {
                window.initDefaultPgOptions(el, index, 'pilgan', 5);
                window.initDefaultPgOptions(el, index, 'kompleks', 5);

                const rows = data.pilihan_jawaban && data.pilihan_jawaban.pernyataan ? data.pilihan_jawaban.pernyataan : [];
                const tbody = el.querySelector('.bs-tbody');

                if (rows.length > 0) {
                    rows.forEach((row, rIdx) => {
                        tbody.insertAdjacentHTML('beforeend', bsTemplate.replace(/{INDEX}/g, index).replace(/{ROW}/g, rIdx));
                        const rowEl = tbody.lastElementChild;
                        rowEl.querySelector('input').value = row.pernyataan || row.text || '';
                        rowEl.querySelector('select').value = (row.benar === true || row.kunci === 'B') ? 'B' : 'S';
                    });
                    // Lanjutkan penomoran baris setelah data lama agar baris baru tidak menimpa indeks.
                    bsRowCounters[index] = rows.length;
                } else {
                    window.addBsRow(el.querySelector('[data-add-bs-row]'));
                }
            } else if (type === 'isian_singkat' || type === 'uraian') {
                window.initDefaultPgOptions(el, index, 'pilgan', 5);
                window.initDefaultPgOptions(el, index, 'kompleks', 5);

                if (type === 'isian_singkat') {
                    el.querySelector(`input[name="soal[${index}][kunci_jawaban_isian]"]`).value = data.kunci_jawaban || '';
                }
            }
        };

        const allLetters = ['A', 'B', 'C', 'D', 'E'];

        function createPgOptionHtml(index, letter, mode) {
            const inputType = mode === 'pilgan' ? 'radio' : 'checkbox';
            const namePrefix = mode === 'pilgan' ? 'pilihan_jawaban_pilgan' : 'pilihan_jawaban_kompleks';
            const keyName = mode === 'pilgan' ? `soal[${index}][kunci_jawaban_pilgan]` : `soal[${index}][kunci_jawaban_kompleks][]`;
            const checkClass = mode === 'pilgan' ? 'border-slate-300' : 'rounded border-slate-300';

            return `<div class="pg-option-row flex items-center gap-2" data-letter="${letter}">
                <label class="flex h-9 w-14 shrink-0 cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs font-extrabold text-slate-700 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700" title="Tandai ${letter} sebagai kunci">
                    <input type="${inputType}" name="${keyName}" value="${letter}" class="h-3.5 w-3.5 ${checkClass} text-emerald-600 focus:ring-emerald-500">${letter}
                </label>
                <input type="text" name="soal[${index}][${namePrefix}][${letter}]" class="${optionInputClass}" placeholder="Opsi ${letter}">
            </div>`;
        }

        window.initDefaultPgOptions = function (el, index, mode, count) {
            const optionContainer = el.querySelector(mode === 'pilgan' ? '.pg-options-container' : '.pgk-options-container');
            if (!optionContainer) return;

            optionContainer.innerHTML = '';
            for (let i = 0; i < count; i++) {
                optionContainer.insertAdjacentHTML('beforeend', createPgOptionHtml(index, allLetters[i], mode));
            }
        };

        window.addPgOption = function (btn, mode) {
            const item = btn.closest('.soal-item');
            const optionContainer = item.querySelector(mode === 'pilgan' ? '.pg-options-container' : '.pgk-options-container');
            const currentCount = optionContainer.querySelectorAll('.pg-option-row').length;

            if (currentCount >= 5) {
                showLmsToast('warning', 'Maksimal 5 opsi jawaban (A-E).');
                return;
            }

            optionContainer.insertAdjacentHTML('beforeend', createPgOptionHtml(item.getAttribute('data-index'), allLetters[currentCount], mode));
        };

        window.removePgOption = function (btn, mode) {
            const optionContainer = btn.closest('.soal-item').querySelector(mode === 'pilgan' ? '.pg-options-container' : '.pgk-options-container');
            const rows = optionContainer.querySelectorAll('.pg-option-row');

            if (rows.length <= 3) {
                showLmsToast('warning', 'Minimal 3 opsi jawaban (A-C).');
                return;
            }

            rows[rows.length - 1].remove();
        };

        window.previewImage = function (input, index) {
            const previewContainer = document.getElementById('imagePreview' + index);
            const previewImg = previewContainer.querySelector('.preview-img');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 2 * 1024 * 1024) {
                    showLmsToast('error', 'Ukuran gambar terlalu besar! Maksimal 2MB.');
                    input.value = '';
                    return;
                }

                if (!['image/jpeg', 'image/png', 'image/jpg', 'image/gif'].includes(file.type)) {
                    showLmsToast('error', 'Format gambar tidak valid! Gunakan JPG, PNG, atau GIF.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
            }
        };

        window.removeImage = function (index) {
            const soalItem = document.querySelector(`.soal-item[data-index="${index}"]`);
            if (!soalItem) return;

            const fileInput = soalItem.querySelector('.image-upload');
            const existingImageInput = soalItem.querySelector('.existing-image-path');
            if (fileInput) fileInput.value = '';
            if (existingImageInput) existingImageInput.value = '';
            document.getElementById('imagePreview' + index)?.classList.add('hidden');
        };

        // --- Aksi sinkron (Simpan semua / Rilis-Tarik) ---
        let targetFormId = null;
        const relatedCount = Number(page.dataset.relatedCount || 0);
        const syncDialog = document.getElementById('syncConfirmDialog');

        window.confirmSyncAction = function (formId, title, message) {
            targetFormId = formId;

            // Tanpa kelas terkait: langsung kirim seperti perilaku sebelumnya.
            if (relatedCount === 0) {
                document.getElementById(formId).submit();
                return;
            }

            document.getElementById('syncModalTitle').textContent = title;
            document.getElementById('syncModalMessage').textContent = message || 'Lanjutkan aksi ini?';
            const cb = document.getElementById('syncConfirmCheckbox');
            if (cb) cb.checked = true;
            syncDialog?.showModal();
        };

        document.getElementById('btnConfirmSync')?.addEventListener('click', () => {
            if (!targetFormId) return;

            const cb = document.getElementById('syncConfirmCheckbox');
            const inputId = { mainForm: 'sync_kelas_main', toggleStatusForm: 'sync_kelas_status', toggleResultForm: 'sync_kelas_result' }[targetFormId];
            const input = inputId ? document.getElementById(inputId) : null;
            if (input) input.value = cb && cb.checked ? 1 : 0;

            syncDialog?.close();
            document.getElementById(targetFormId).submit();
        });

        // --- Delegasi event ---
        document.addEventListener('click', async (event) => {
            const target = event.target;

            const openDialog = target.closest('[data-open-dialog]');
            if (openDialog) {
                document.getElementById(openDialog.dataset.openDialog)?.showModal();
                return;
            }

            const closeDialog = target.closest('[data-close-dialog]');
            if (closeDialog) {
                closeDialog.closest('dialog')?.close();
                return;
            }

            if (target.matches('dialog')) {
                target.close();
                return;
            }

            if (target.closest('[data-open-ai-sidebar]')) {
                event.preventDefault();
                if (typeof window.openAiSidebar === 'function') {
                    window.openAiSidebar();
                } else {
                    showLmsToast('warning', 'AI Question Generator gagal dimuat.');
                }
                return;
            }

            if (!page.contains(target) && !container.contains(target)) {
                return;
            }

            const addButton = target.closest('[data-add-question]');
            const removeButton = target.closest('[data-remove-question]');
            const toggleButton = target.closest('[data-toggle-soal]');
            const confirmRemoveButton = target.closest('[data-confirm-remove]');
            const syncButton = target.closest('[data-sync-action]');
            const removeImageButton = target.closest('[data-remove-image]');
            const addPgButton = target.closest('[data-add-pg-option]');
            const removePgButton = target.closest('[data-remove-pg-option]');
            const addBsButton = target.closest('[data-add-bs-row]');

            if (addButton) {
                event.preventDefault();
                window.addQuestion();
            } else if (removeButton) {
                event.preventDefault();
                window.removeQuestion(event, removeButton);
            } else if (toggleButton) {
                event.preventDefault();
                toggleSoal(toggleButton.closest('.soal-item'));
            } else if (confirmRemoveButton) {
                event.preventDefault();
                window.confirmRemoveVal();
            } else if (syncButton) {
                event.preventDefault();
                window.confirmSyncAction(syncButton.dataset.syncForm, syncButton.dataset.syncTitle, syncButton.dataset.syncMessage);
            } else if (removeImageButton) {
                event.preventDefault();
                window.removeImage(removeImageButton.dataset.removeImage);
            } else if (addPgButton) {
                event.preventDefault();
                window.addPgOption(addPgButton, addPgButton.dataset.addPgOption);
            } else if (removePgButton) {
                event.preventDefault();
                window.removePgOption(removePgButton, removePgButton.dataset.removePgOption);
            } else if (addBsButton) {
                event.preventDefault();
                window.addBsRow(addBsButton);
            }
        });

        page.addEventListener('change', (event) => {
            if (event.target.matches('.type-select')) {
                window.changeType(event.target);
            } else if (event.target.matches('.image-upload')) {
                window.previewImage(event.target, event.target.dataset.previewImage);
            }
        });

        page.addEventListener('input', (event) => {
            if (event.target.matches('.question-input')) {
                window.updatePreview(event.target);
            }
        });

        if (existingData && existingData.length > 0) {
            existingData.forEach((soal) => window.addQuestion(soal));
        } else {
            window.addQuestion();
        }
    });
})();
