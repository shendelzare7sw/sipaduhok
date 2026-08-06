document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.admin-ai-settings-page');

    if (!page) {
        return;
    }

    const providerSelect = page.querySelector('#ai_provider');
    const modelSelect = page.querySelector('#ai_model');
    const groqField = page.querySelector('#groq_field');
    const geminiField = page.querySelector('#gemini_field');
    const contextRestrictionToggle = page.querySelector('#context_restriction_enabled');
    const contextRestrictionHidden = page.querySelector('#context_restriction_enabled_hidden');
    const questionGeneratorToggle = page.querySelector('#ai_question_generator_enabled');
    const questionGeneratorHidden = page.querySelector('#ai_question_generator_enabled_hidden');

    const syncBooleanHidden = (toggle, hidden) => {
        if (!toggle || !hidden) {
            return;
        }

        hidden.value = toggle.checked ? '1' : '0';
    };

    syncBooleanHidden(contextRestrictionToggle, contextRestrictionHidden);
    syncBooleanHidden(questionGeneratorToggle, questionGeneratorHidden);

    contextRestrictionToggle?.addEventListener('change', () => {
        syncBooleanHidden(contextRestrictionToggle, contextRestrictionHidden);
    });

    questionGeneratorToggle?.addEventListener('change', () => {
        syncBooleanHidden(questionGeneratorToggle, questionGeneratorHidden);
    });

    // Daftar model diambil dari <option> yang sudah dirender server (sumbernya
    // config/ai-models.php), bukan disalin ulang di sini - dulu salinan JS ini
    // ikut basi saat penyedia mematikan sebuah model.
    const modelsByProvider = (() => {
        const hasil = { groq: [], gemini: [] };
        document.querySelectorAll('#ai_model optgroup').forEach((grup) => {
            const kunci = /gemini/i.test(grup.label) ? 'gemini' : 'groq';
            grup.querySelectorAll('option').forEach((opt) => hasil[kunci].push(opt.value));
        });
        return hasil;
    })();

    const setProviderFieldVisibility = (field, isVisible) => {
        field?.classList.toggle('is-hidden', !isVisible);
    };

    const filterModels = (provider) => {
        if (!modelSelect) {
            return;
        }

        const options = Array.from(modelSelect.querySelectorAll('option'));
        const allowedModels = modelsByProvider[provider] || [];

        options.forEach((option) => {
            option.hidden = !allowedModels.includes(option.value);
        });

        modelSelect.querySelectorAll('optgroup').forEach((optgroup) => {
            const hasVisibleOptions = Array.from(optgroup.querySelectorAll('option')).some((option) => !option.hidden);
            optgroup.hidden = !hasVisibleOptions;
        });

        const currentOption = options.find((option) => option.value === modelSelect.value);

        if (!currentOption || currentOption.hidden) {
            const firstVisible = options.find((option) => !option.hidden);

            if (firstVisible) {
                modelSelect.value = firstVisible.value;
            }
        }
    };

    const toggleProviderFields = () => {
        if (!providerSelect) {
            return;
        }

        const provider = providerSelect.value;

        setProviderFieldVisibility(groqField, provider !== 'gemini');
        setProviderFieldVisibility(geminiField, provider === 'gemini');
        filterModels(provider);
    };

    toggleProviderFields();
    providerSelect?.addEventListener('change', toggleProviderFields);

    const togglePasswordVisibility = (button, input) => {
        if (!button || !input) {
            return;
        }

        button.addEventListener('click', () => {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            const icon = button.querySelector('i');

            input.setAttribute('type', type);
            icon?.classList.toggle('fa-eye');
            icon?.classList.toggle('fa-eye-slash');
        });
    };

    const toggleGroqApiKey = page.querySelector('#toggleGroqApiKey');
    const groqApiKeyInput = page.querySelector('#groq_api_key');
    const toggleGeminiApiKey = page.querySelector('#toggleGeminiApiKey');
    const geminiApiKeyInput = page.querySelector('#gemini_api_key');

    togglePasswordVisibility(toggleGroqApiKey, groqApiKeyInput);
    togglePasswordVisibility(toggleGeminiApiKey, geminiApiKeyInput);

    const testBtn = page.querySelector('#testConnectionBtn');
    const alertEl = page.querySelector('#connectionAlert');
    const alertMsg = page.querySelector('#connectionMessage');
    const alertTitle = page.querySelector('#connectionTitle');
    const alertIcon = page.querySelector('#connectionIcon');

    let alertTimeoutId = null;

    const hideAlertSmooth = (element) => {
        if (!element) {
            return;
        }

        element.classList.add('fading-out');

        setTimeout(() => {
            element.classList.add('d-none');
            element.classList.remove('fading-out', 'alert-success', 'alert-danger', 'alert-warning');
            void element.offsetWidth;
        }, 350);
    };

    const showAlertSmooth = (element) => {
        if (!element) {
            return;
        }

        element.classList.remove('d-none', 'fading-out');
        void element.offsetWidth;
        element.classList.add('showing');
    };

    const resetConnectionAlert = () => {
        if (!alertEl) {
            return;
        }

        if (alertTimeoutId) {
            clearTimeout(alertTimeoutId);
            alertTimeoutId = null;
        }

        alertEl.classList.add('d-none');
        alertEl.classList.remove('alert-success', 'alert-danger', 'alert-info', 'alert-warning', 'fading-out', 'showing');
        void alertEl.offsetWidth;
    };

    const setConnectionAlert = ({ type, title, message, icon }) => {
        if (!alertEl || !alertTitle || !alertMsg || !alertIcon) {
            return;
        }

        if (alertTimeoutId) {
            clearTimeout(alertTimeoutId);
            alertTimeoutId = null;
        }

        alertEl.classList.add(type);
        alertTitle.textContent = title;
        alertMsg.textContent = message;
        alertIcon.className = icon;
        showAlertSmooth(alertEl);
    };

    testBtn?.addEventListener('click', () => {
        if (!providerSelect || !modelSelect || !groqApiKeyInput || !geminiApiKeyInput) {
            return;
        }

        resetConnectionAlert();

        const provider = providerSelect.value;
        const apiKey = provider === 'groq' ? groqApiKeyInput.value : geminiApiKeyInput.value;

        if (!apiKey) {
            setConnectionAlert({
                type: 'alert-warning',
                title: 'Peringatan!',
                message: `API Key untuk ${provider === 'groq' ? 'Groq Cloud' : 'Google Gemini'} belum diisi.`,
                icon: 'fas fa-exclamation-triangle me-2 fs-4',
            });

            alertTimeoutId = setTimeout(() => hideAlertSmooth(alertEl), 5000);
            return;
        }

        const originalText = testBtn.innerHTML;
        const testUrl = new URL(page.dataset.testUrl || window.location.pathname, window.location.origin).pathname;
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000);

        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Testing...';
        testBtn.disabled = true;

        fetch(testUrl, {
            method: 'POST',
            signal: controller.signal,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': page.dataset.csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                api_key: apiKey,
                model: modelSelect.value,
                provider,
            }),
        })
            .then((response) => {
                clearTimeout(timeoutId);

                const contentType = response.headers.get('Content-Type') || '';

                if (!contentType.includes('application/json')) {
                    throw new Error(`Server error (HTTP ${response.status}). Pastikan APP_URL di .env sudah benar dan jalankan php artisan config:clear.`);
                }

                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    setConnectionAlert({
                        type: 'alert-success',
                        title: 'Berhasil!',
                        message: data.message,
                        icon: 'fas fa-check-circle me-2 fs-4',
                    });

                    alertTimeoutId = setTimeout(() => hideAlertSmooth(alertEl), 5000);
                    return;
                }

                setConnectionAlert({
                    type: 'alert-danger',
                    title: 'Gagal!',
                    message: data.message,
                    icon: 'fas fa-times-circle me-2 fs-4',
                });

                alertTimeoutId = setTimeout(() => hideAlertSmooth(alertEl), 6000);
            })
            .catch((error) => {
                clearTimeout(timeoutId);

                setConnectionAlert({
                    type: 'alert-danger',
                    title: 'Error Sistem',
                    message: error.name === 'AbortError'
                        ? 'Request timeout (>30 detik). Server terlalu lama merespons.'
                        : error.message || 'Terjadi kesalahan tidak diketahui.',
                    icon: 'fas fa-exclamation-triangle me-2 fs-4',
                });

                alertTimeoutId = setTimeout(() => hideAlertSmooth(alertEl), 6000);
            })
            .finally(() => {
                testBtn.innerHTML = originalText;
                testBtn.disabled = false;
            });
    });
});
