document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-tugas-koreksi-show-page');

    if (!page) {
        return;
    }

    const aiButton = page.querySelector('#aiAssistBtn');

    if (!aiButton) {
        return;
    }

    const toastEl = page.querySelector('#aiToast');
    const toast = toastEl && window.bootstrap ? new window.bootstrap.Toast(toastEl) : null;
    const toastMsg = page.querySelector('#aiToastMessage');
    const aiSuggestUrl = page.dataset.aiSuggestUrl || '';
    const csrfToken = page.dataset.csrfToken || '';
    const hasAnswer = page.dataset.hasAnswer === 'true';
    let isProcessing = false;

    const showToast = (message) => {
        if (toastMsg) {
            toastMsg.innerHTML = message;
        }

        if (toast) {
            toast.show();
        }
    };

    aiButton.addEventListener('click', function handleAiAssistClick() {
        if (isProcessing) {
            return;
        }

        if (!hasAnswer) {
            showToast('<i class="fas fa-exclamation-circle text-warning me-1"></i> Belum ada jawaban siswa untuk dianalisis.');
            return;
        }

        if (!aiSuggestUrl || !csrfToken) {
            showToast('<i class="fas fa-exclamation-triangle text-danger me-1"></i> Konfigurasi AI Assistant belum lengkap.');
            return;
        }

        isProcessing = true;

        const originalContent = this.innerHTML;
        let seconds = 0;
        const buttonRef = this;

        buttonRef.disabled = true;
        buttonRef.classList.add('btn-ai-loading');

        const updateTimer = () => {
            buttonRef.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Menganalisis... <span class="badge bg-light text-dark ms-1">${seconds}s</span>`;
            showToast(`<i class="fas fa-spinner fa-spin text-primary me-1"></i> Sedang menganalisis jawaban (Vision AI)... <strong>${seconds}s</strong>`);
        };

        updateTimer();

        const timerInterval = window.setInterval(() => {
            seconds += 1;
            updateTimer();
        }, 1000);

        fetch(aiSuggestUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({}),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    throw new Error(data.feedback || 'Terjadi kesalahan pada AI.');
                }

                const scoreInput = page.querySelector('#nilaiInput');
                const feedbackInput = page.querySelector('#feedbackInput');

                if (scoreInput) {
                    scoreInput.value = data.score;
                    scoreInput.classList.add('bg-success', 'text-white', 'bg-opacity-25');
                }

                if (feedbackInput) {
                    feedbackInput.value = `[AI Suggestion] ${data.feedback}\n\n` + feedbackInput.value;
                    feedbackInput.classList.add('bg-info', 'text-white', 'bg-opacity-10');
                }

                window.setTimeout(() => {
                    scoreInput?.classList.remove('bg-success', 'text-white', 'bg-opacity-25');
                    feedbackInput?.classList.remove('bg-info', 'text-white', 'bg-opacity-10');
                }, 2000);

                showToast(`<i class="fas fa-check-circle text-success me-1"></i> Analisis selesai dalam <strong>${seconds}s</strong>! Saran skor: <strong>${data.score}</strong>`);
            })
            .catch((error) => {
                console.error(error);
                showToast(`<i class="fas fa-exclamation-triangle text-danger me-1"></i> Gagal (${seconds}s): ${error.message}`);
            })
            .finally(() => {
                window.clearInterval(timerInterval);
                buttonRef.innerHTML = originalContent;
                buttonRef.disabled = false;
                buttonRef.classList.remove('btn-ai-loading');
                isProcessing = false;
            });
    });
});
