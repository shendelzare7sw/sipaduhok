document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-ujian-koreksi-page');

    if (!page) {
        return;
    }

    const aiButtons = page.querySelectorAll('.ai-assist-btn');
    const toastEl = page.querySelector('#aiToast');
    const toast = toastEl && window.bootstrap ? new window.bootstrap.Toast(toastEl) : null;
    const toastMsg = page.querySelector('#aiToastMessage');
    const processingButtons = new Set();
    const aiUrlTemplate = page.dataset.aiUrlTemplate || '';
    const csrfToken = page.dataset.csrfToken || '';

    const showToast = (message, useHtml = false) => {
        if (!toastMsg) {
            return;
        }

        if (useHtml) {
            toastMsg.innerHTML = message;
        } else {
            toastMsg.textContent = message;
        }

        if (toast) {
            toast.show();
        }
    };

    aiButtons.forEach((button) => {
        button.addEventListener('click', function handleAiAssistClick() {
            const soalId = this.dataset.soalId;

            if (processingButtons.has(soalId)) {
                return;
            }

            const answer = this.dataset.answer;

            if (!answer || answer === '-') {
                showToast('Belum ada jawaban siswa untuk dianalisis.');
                return;
            }

            if (!aiUrlTemplate || !csrfToken) {
                showToast('Konfigurasi AI Assistant belum lengkap.');
                return;
            }

            processingButtons.add(soalId);

            const originalContent = this.innerHTML;
            let seconds = 0;
            const buttonRef = this;

            buttonRef.disabled = true;
            buttonRef.classList.add('btn-ai-loading');

            const updateTimer = () => {
                buttonRef.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Menganalisis... <span class="badge bg-light text-dark ms-1">${seconds}s</span>`;
            };

            updateTimer();
            const timerInterval = window.setInterval(() => {
                seconds += 1;
                updateTimer();
            }, 1000);

            const url = aiUrlTemplate.replace('SOAL_ID_PLACEHOLDER', encodeURIComponent(soalId));

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ answer }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) {
                        throw new Error(data.feedback || 'Terjadi kesalahan pada AI.');
                    }

                    const scoreInput = page.querySelector(`#nilai_${soalId}`);
                    const feedbackInput = page.querySelector(`#feedback_${soalId}`);

                    if (scoreInput) {
                        scoreInput.value = data.score;
                        scoreInput.classList.add('bg-success', 'text-white', 'bg-opacity-25');
                    }

                    if (feedbackInput) {
                        const existingFeedback = feedbackInput.value.trim();
                        feedbackInput.value = `[AI Suggestion] ${data.feedback}${existingFeedback ? '\n\n' + existingFeedback : ''}`;
                        feedbackInput.classList.add('bg-info', 'text-white', 'bg-opacity-10');
                    }

                    window.setTimeout(() => {
                        scoreInput?.classList.remove('bg-success', 'text-white', 'bg-opacity-25');
                        feedbackInput?.classList.remove('bg-info', 'text-white', 'bg-opacity-10');
                    }, 2000);

                    showToast(
                        `<i class="fas fa-check-circle text-success me-1"></i> Analisis selesai dalam <strong>${seconds}s</strong>! Saran skor: <strong>${data.score}</strong>`,
                        true,
                    );
                })
                .catch((error) => {
                    console.error(error);
                    showToast(
                        `<i class="fas fa-exclamation-triangle text-danger me-1"></i> Gagal (${seconds}s): ${error.message}`,
                        true,
                    );
                })
                .finally(() => {
                    window.clearInterval(timerInterval);
                    buttonRef.innerHTML = originalContent;
                    buttonRef.disabled = false;
                    buttonRef.classList.remove('btn-ai-loading');
                    processingButtons.delete(soalId);
                });
        });
    });
});
