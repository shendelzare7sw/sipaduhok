import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

function showDialog(options, callback) {
    Swal.fire(options).then((result) => {
        callback(result.isConfirmed);
    });
}

function submitForm(form) {
    if (form) {
        form.submit();
    }
}

function setupRetake(page) {
    page.querySelectorAll('[data-confirm-retake]').forEach((button) => {
        button.addEventListener('click', () => {
            showDialog({
                title: 'Kerjakan Ulang?',
                text: 'Jawaban dan nilai Anda sebelumnya akan di-reset. Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kerjakan Ulang',
                cancelButtonText: 'Batal',
            }, (confirmed) => {
                if (confirmed) {
                    submitForm(document.getElementById('form-retake'));
                }
            });
        });
    });
}

function setupWorkPage(workPage) {
    const examForm = workPage.querySelector('#examForm');
    const timerDisplay = workPage.querySelector('#timer-display');
    const durasiMenit = Number(workPage.dataset.durationMinutes || 0);
    const startTime = new Date(workPage.dataset.startTime || '').getTime();
    const isUnlimited = durasiMenit === 0;
    const endTime = isUnlimited || Number.isNaN(startTime) ? null : startTime + (durasiMenit * 60 * 1000);

    const autoSaveAnswer = (soalId, jawaban) => {
        if (!workPage.dataset.autosaveUrl) {
            return;
        }

        fetch(workPage.dataset.autosaveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': workPage.dataset.csrfToken || '',
            },
            body: JSON.stringify({
                soal_id: soalId,
                jawaban,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    console.error('Autosave failed:', data.message);
                }
            })
            .catch((error) => console.error('Autosave error:', error));
    };

    const updateTimer = () => {
        if (!timerDisplay) {
            return;
        }

        if (isUnlimited) {
            timerDisplay.textContent = 'NO LIMIT';
            return;
        }

        const distance = endTime - Date.now();

        if (distance < 0) {
            timerDisplay.textContent = '00:00:00';
            showDialog({
                title: 'Waktu Habis!',
                text: 'Latihan akan disubmit otomatis.',
                icon: 'warning',
                timer: 2000,
                showConfirmButton: false,
            }, () => submitForm(examForm));
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerDisplay.textContent = [
            String(hours).padStart(2, '0'),
            String(minutes).padStart(2, '0'),
            String(seconds).padStart(2, '0'),
        ].join(':');
    };

    const updateKompleks = (soalId) => {
        const checkboxes = workPage.querySelectorAll(`.kompleks-cb[data-soal-id="${soalId}"]:checked`);
        const selected = Array.from(checkboxes).map((checkbox) => checkbox.value);
        const value = JSON.stringify(selected);
        const hiddenInput = workPage.querySelector(`#kompleks-hidden-${soalId}`);

        if (hiddenInput) {
            hiddenInput.value = value;
        }

        autoSaveAnswer(soalId, value);
    };

    const updateBenarSalah = (soalId, totalPernyataan) => {
        const answers = [];

        for (let i = 0; i < totalPernyataan; i += 1) {
            const radio = workPage.querySelector(`input[name="bs_${soalId}_${i}"]:checked`);
            answers.push(radio ? radio.value === 'true' : null);
        }

        const value = JSON.stringify(answers);
        const hiddenInput = workPage.querySelector(`#bs-hidden-${soalId}`);

        if (hiddenInput) {
            hiddenInput.value = value;
        }

        autoSaveAnswer(soalId, value);
    };

    workPage.querySelectorAll('[data-finish-exam]').forEach((button) => {
        button.addEventListener('click', () => {
            showDialog({
                title: 'Kirim Jawaban?',
                text: 'Pastikan Anda sudah memeriksa semua jawaban.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Periksa Lagi',
            }, (confirmed) => {
                if (confirmed) {
                    submitForm(examForm);
                }
            });
        });
    });

    workPage.addEventListener('change', (event) => {
        const target = event.target;

        if (target.matches('[data-autosave-answer]')) {
            autoSaveAnswer(target.dataset.soalId, target.value);
            return;
        }

        if (target.matches('.kompleks-cb')) {
            updateKompleks(target.dataset.soalId);
            return;
        }

        if (target.matches('[data-benar-salah-answer]')) {
            updateBenarSalah(target.dataset.soalId, Number(target.dataset.totalPernyataan || 0));
        }
    });

    workPage.addEventListener('input', (event) => {
        const target = event.target;

        if (target.matches('[data-autosave-answer]')) {
            autoSaveAnswer(target.dataset.soalId, target.value);
        }
    });

    if (!isUnlimited) {
        window.setInterval(updateTimer, 1000);
    }

    updateTimer();
}

document.addEventListener('DOMContentLoaded', () => {
    const showPage = document.querySelector('.siswa-lms-latihan-show-page');

    if (showPage) {
        setupRetake(showPage);
    }

    const workPage = document.querySelector('.siswa-lms-latihan-work-page');

    if (workPage) {
        setupWorkPage(workPage);
    }
});
