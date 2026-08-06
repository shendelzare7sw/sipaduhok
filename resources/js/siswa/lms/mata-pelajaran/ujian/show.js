import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

function parseEncodedJson(value, fallback) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(window.atob(value));
    } catch (error) {
        console.error('Failed to parse encoded JSON:', error);
        return fallback;
    }
}

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
                confirmButtonText: 'Ya, Kerjakan Ulang!',
                cancelButtonText: 'Batal',
            }, (confirmed) => {
                if (confirmed) {
                    submitForm(document.getElementById('form-retake'));
                }
            });
        });
    });
}

function setupExam(page) {
    const examForm = page.querySelector('#examForm');
    const questionMeta = parseEncodedJson(page.dataset.questionMeta, []);
    const answersState = parseEncodedJson(page.dataset.answersState, []);
    const totalQuestions = Number(page.dataset.totalQuestions || questionMeta.length || 0);
    const examType = page.dataset.examType || 'ujian';
    const csrfToken = page.dataset.csrfToken || '';
    const monitoringUrl = page.dataset.monitoringUrl || '';
    const autosaveUrl = page.dataset.autosaveUrl || '';
    const storageKey = page.dataset.storageKey || 'doubtState';
    const savedDoubts = localStorage.getItem(storageKey);
    const doubtState = savedDoubts ? JSON.parse(savedDoubts) : new Array(totalQuestions).fill(false);
    const visitedState = new Array(totalQuestions).fill(false);
    const durasiMenit = Number(page.dataset.durationMinutes || 0);
    const startTime = new Date(page.dataset.startTime || '').getTime();
    const isUnlimited = durasiMenit === 0;
    const endTime = isUnlimited || Number.isNaN(startTime) ? null : startTime + (durasiMenit * 60 * 1000);
    let currentIndex = 0;

    if (totalQuestions > 0) {
        visitedState[0] = true;
    }

    const currentQuestionMeta = () => questionMeta[currentIndex] || null;

    const buildMonitoringStatuses = () => questionMeta.map((meta, index) => ({
        soal_id: meta.soal_id,
        nomor_soal: meta.nomor_soal,
        is_visited: visitedState[index] || index === currentIndex,
        is_answered: !!answersState[index],
        is_doubt: !!doubtState[index],
    }));

    const sendMonitoringEvent = (eventType, options = {}) => {
        if (examType === 'latihan' || !monitoringUrl) {
            return Promise.resolve();
        }

        const meta = currentQuestionMeta();
        const includeStatuses = ['heartbeat', 'question_opened', 'doubt_updated'].includes(eventType);
        const payload = {
            event_type: eventType,
            current_soal_id: meta ? meta.soal_id : null,
            current_nomor_soal: meta ? meta.nomor_soal : null,
            metadata: options.metadata || {},
        };

        if (includeStatuses) {
            payload.statuses = buildMonitoringStatuses();
        }

        return fetch(monitoringUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
            keepalive: ['focus_lost', 'focus_returned'].includes(eventType),
        }).catch((error) => {
            console.error('Monitoring error:', error);
        });
    };

    const updateNavColor = (index) => {
        const navItem = page.querySelector(`#nav-item-${index}`);
        if (!navItem) {
            return;
        }

        navItem.classList.remove('answered', 'doubt');

        if (doubtState[index]) {
            navItem.classList.add('doubt');
        } else if (answersState[index]) {
            navItem.classList.add('answered');
        }
    };

    const syncRaguUI = () => {
        const cbRagu = page.querySelector('#cb-ragu');
        const labelRagu = page.querySelector('#label-ragu');

        if (!cbRagu || !labelRagu) {
            return;
        }

        cbRagu.checked = !!doubtState[currentIndex];
        labelRagu.classList.toggle('is-selected', !!doubtState[currentIndex]);
    };

    const updateUI = () => {
        const questionDisplay = page.querySelector('#q-no-display');
        const prevButton = page.querySelector('#btn-prev');
        const nextButton = page.querySelector('#btn-next');

        if (questionDisplay) {
            questionDisplay.innerText = currentIndex + 1;
        }

        if (prevButton) {
            prevButton.disabled = currentIndex === 0;
        }

        if (nextButton) {
            nextButton.disabled = currentIndex === totalQuestions - 1;
        }

        syncRaguUI();

        page.querySelectorAll('.q-nav-item').forEach((item, index) => {
            item.classList.toggle('active', index === currentIndex);
        });
    };

    const jumpToQuestion = (index, source = 'navigation') => {
        const currentItem = page.querySelector(`#q-item-${currentIndex}`);
        const nextItem = page.querySelector(`#q-item-${index}`);

        if (!nextItem) {
            return;
        }

        currentItem?.classList.remove('is-active');
        nextItem.classList.add('is-active');
        currentIndex = index;
        visitedState[index] = true;
        updateUI();
        sendMonitoringEvent('question_opened', {
            metadata: { source },
        });
    };

    const selectOption = (index, value, soalId) => {
        const normalizedValue = value === null || value === undefined ? '' : String(value);
        answersState[index] = normalizedValue.trim() !== '' && normalizedValue.trim() !== '-';
        updateNavColor(index);

        if (soalId) {
            autoSaveAnswer(soalId, value, index);
        }
    };

    const autoSaveAnswer = (soalId, jawaban, index = currentIndex) => {
        if (!autosaveUrl) {
            return;
        }

        const meta = questionMeta[index] || null;

        fetch(autosaveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                soal_id: soalId,
                nomor_soal: meta ? meta.nomor_soal : null,
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

    const toggleRagu = (isChecked) => {
        doubtState[currentIndex] = isChecked === undefined ? !doubtState[currentIndex] : isChecked;
        localStorage.setItem(storageKey, JSON.stringify(doubtState));
        updateNavColor(currentIndex);
        syncRaguUI();
        sendMonitoringEvent('doubt_updated', {
            metadata: {
                is_doubt: doubtState[currentIndex],
            },
        });
    };

    const updateKompleks = (soalId, index) => {
        const checkboxes = page.querySelectorAll(`.kompleks-cb[data-soal-id="${soalId}"]:checked`);
        const selected = Array.from(checkboxes).map((checkbox) => checkbox.value);
        const value = JSON.stringify(selected);
        const hiddenInput = page.querySelector(`#kompleks-hidden-${soalId}`);

        if (hiddenInput) {
            hiddenInput.value = value;
        }

        selectOption(index, selected.length > 0 ? value : '', soalId);
    };

    const updateBenarSalah = (soalId, totalPernyataan, index) => {
        const answers = [];
        let answeredCount = 0;

        for (let i = 0; i < totalPernyataan; i += 1) {
            const radio = page.querySelector(`input[name="bs_${soalId}_${i}"]:checked`);
            if (radio) {
                answers.push(radio.value === 'true');
                answeredCount += 1;
            } else {
                answers.push(null);
            }
        }

        const value = JSON.stringify(answers);
        const hiddenInput = page.querySelector(`#bs-hidden-${soalId}`);

        if (hiddenInput) {
            hiddenInput.value = value;
        }

        selectOption(index, answeredCount === totalPernyataan ? value : '', soalId);
    };

    const finishExam = () => {
        const unanswered = answersState.filter((answered) => !answered).length;
        const doubts = doubtState.filter(Boolean).length;
        const isLatihan = examType === 'latihan';

        if (!isLatihan && (unanswered > 0 || doubts > 0)) {
            let errorMsg = 'Anda tidak dapat mengumpulkan ujian karena ada soal yang belum selesai.\n';
            if (unanswered > 0) errorMsg += `\n- Terdapat ${unanswered} soal belum dijawab.`;
            if (doubts > 0) errorMsg += `\n- Terdapat ${doubts} soal ditandai ragu-ragu.`;
            errorMsg += '\n\nSilakan lengkapi dan hilangkan tanda ragu-ragu sebelum submit.';

            showDialog({
                title: 'Peringatan!',
                text: errorMsg,
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Tutup',
            }, () => {});
            return;
        }

        let message = '';
        if (unanswered > 0) message += `Masih ada ${unanswered} soal belum dijawab.\n`;
        if (doubts > 0) message += `Masih ada ${doubts} soal ditandai ragu-ragu.\n`;
        message += '\nApakah Anda yakin ingin menyelesaikan sesi ini?';

        showDialog({
            title: 'Konfirmasi Pengumpulan',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal',
        }, (confirmed) => {
            if (confirmed) {
                localStorage.removeItem(storageKey);
                submitForm(examForm);
            }
        });
    };

    const updateTimer = () => {
        const timerMain = page.querySelector('#timer-display-main');
        const mobileTimers = page.querySelectorAll('.mobile-timer');

        if (isUnlimited) {
            if (timerMain) timerMain.textContent = 'NO LIMIT';
            mobileTimers.forEach((timer) => {
                timer.textContent = 'NO LIMIT';
            });
            return;
        }

        const distance = endTime - Date.now();

        if (distance < 0) {
            if (timerMain) timerMain.textContent = '00:00:00';
            mobileTimers.forEach((timer) => {
                timer.textContent = '00:00:00';
            });

            showDialog({
                title: 'Waktu Habis!',
                text: 'Ujian akan disubmit otomatis.',
                icon: 'warning',
                timer: 2000,
                showConfirmButton: false,
            }, () => submitForm(examForm));
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        const timerText = [
            String(hours).padStart(2, '0'),
            String(minutes).padStart(2, '0'),
            String(seconds).padStart(2, '0'),
        ].join(':');

        if (timerMain) timerMain.textContent = timerText;
        mobileTimers.forEach((timer) => {
            timer.textContent = timerText;
        });
    };

    page.addEventListener('click', (event) => {
        const target = event.target;
        const navItem = target.closest('[data-jump-question]');

        if (navItem) {
            jumpToQuestion(Number(navItem.dataset.jumpQuestion));
            return;
        }

        if (target.closest('[data-prev-question]')) {
            if (currentIndex > 0) {
                jumpToQuestion(currentIndex - 1, 'prev_button');
            }
            return;
        }

        if (target.closest('[data-next-question]')) {
            if (currentIndex < totalQuestions - 1) {
                jumpToQuestion(currentIndex + 1, 'next_button');
            }
            return;
        }

        if (target.closest('[data-finish-exam]')) {
            finishExam();
        }
    });

    page.addEventListener('change', (event) => {
        const target = event.target;

        if (target.matches('[data-answer-choice]')) {
            selectOption(Number(target.dataset.index), target.value, target.dataset.soalId);
            return;
        }

        if (target.matches('.kompleks-cb')) {
            updateKompleks(target.dataset.soalId, Number(target.dataset.index));
            return;
        }

        if (target.matches('[data-benar-salah-answer]')) {
            updateBenarSalah(
                target.dataset.soalId,
                Number(target.dataset.totalPernyataan || 0),
                Number(target.dataset.index),
            );
            return;
        }

        if (target.matches('[data-toggle-doubt]')) {
            toggleRagu(target.checked);
        }
    });

    page.addEventListener('input', (event) => {
        const target = event.target;

        if (target.matches('[data-answer-text]')) {
            selectOption(Number(target.dataset.index), target.value, target.dataset.soalId);
        }
    });

    page.querySelectorAll('[data-confirm-empty-submit]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (!window.confirm('Anda akan mengakhiri ujian tanpa menjawab soal. Lanjutkan?')) {
                event.preventDefault();
            }
        });
    });

    for (let i = 0; i < totalQuestions; i += 1) {
        updateNavColor(i);
    }

    updateTimer();
    let timerInterval = null;
    if (!isUnlimited) {
        timerInterval = window.setInterval(updateTimer, 1000);
    }

    updateUI();
    sendMonitoringEvent('question_opened', {
        metadata: { source: 'initial_load' },
    });
    sendMonitoringEvent('heartbeat');
    window.setInterval(() => {
        sendMonitoringEvent('heartbeat');
    }, 5000);

    // Enforce fullscreen if user tries to exit during exam
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement && !examForm.submitted) {
            registerFocusLost('exited_fullscreen');
        }
    });

    history.pushState(null, null, location.href);
    window.onpopstate = () => {
        history.go(1);
    };

    let focusLostActive = false;

    const registerFocusLost = (trigger) => {
        if (examType === 'latihan' || focusLostActive) {
            return;
        }

        focusLostActive = true;
        sendMonitoringEvent('focus_lost', {
            metadata: { trigger },
        });
        showDialog({
            title: 'Peringatan Kecurangan!',
            text: 'Anda dilarang meninggalkan atau berpindah tab saat ujian berlangsung! Percobaan ini telah dicatat sistem.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Kembali Fokus',
        }, () => {});
    };

    const registerFocusReturned = (trigger) => {
        if (examType === 'latihan' || !focusLostActive) {
            return;
        }

        focusLostActive = false;
        sendMonitoringEvent('focus_returned', {
            metadata: { trigger },
        });
    };

    window.addEventListener('blur', () => {
        registerFocusLost('window_blur');
    });

    window.addEventListener('focus', () => {
        updateTimer();
        registerFocusReturned('window_focus');
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            registerFocusLost('visibility_hidden');
        } else {
            updateTimer();
            registerFocusReturned('visibility_visible');
        }
    });

    document.addEventListener('contextmenu', (event) => {
        if (examType !== 'latihan') {
            event.preventDefault();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (examType !== 'latihan' && event.ctrlKey && ['c', 'v', 'u', 'i'].includes(event.key.toLowerCase())) {
            event.preventDefault();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const showPage = document.querySelector('.siswa-lms-ujian-show-page');
    if (showPage) {
        setupRetake(showPage);
        
        // Intercept "Mulai Ujian Sekarang" form to enforce fullscreen immediately
        const formMulai = showPage.querySelector('form[action*="/mulai"]');
        if (formMulai) {
            formMulai.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Request fullscreen on click
                const docElm = document.documentElement;
                if (docElm.requestFullscreen) docElm.requestFullscreen().catch(()=>{});
                else if (docElm.webkitRequestFullscreen) docElm.webkitRequestFullscreen();
                else if (docElm.msRequestFullscreen) docElm.msRequestFullscreen();
                
                const btnMulai = formMulai.querySelector('button[type="submit"]');
                if (btnMulai) {
                    btnMulai.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mempersiapkan Ujian...';
                    btnMulai.disabled = true;
                }
                
                try {
                    const response = await fetch(formMulai.action, {
                        method: 'POST',
                        body: new FormData(formMulai),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    if (response.ok) {
                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Import CSS and JS from the new head
                        Array.from(doc.head.querySelectorAll('link[rel="stylesheet"], script[src]')).forEach(child => {
                            const isLink = child.tagName === 'LINK';
                            const url = isLink ? child.href : child.src;
                            if (url && !document.head.querySelector(`${isLink ? 'link' : 'script'}[${isLink ? 'href' : 'src'}="${url}"]`)) {
                                const newChild = document.createElement(child.tagName);
                                Array.from(child.attributes).forEach(attr => newChild.setAttribute(attr.name, attr.value));
                                document.head.appendChild(newChild);
                            }
                        });

                        // Replace body content without destroying document.documentElement
                        document.body.innerHTML = doc.body.innerHTML;
                        document.body.className = doc.body.className;

                        // Re-initialize exam since DOMContentLoaded won't fire again
                        const newWorkPage = document.querySelector('.siswa-lms-ujian-work-page');
                        if (newWorkPage) {
                            setupExam(newWorkPage);
                        }
                    } else {
                        formMulai.submit(); // fallback
                    }
                } catch (error) {
                    formMulai.submit(); // fallback
                }
            });
        }
    }

    const workPage = document.querySelector('.siswa-lms-ujian-work-page');
    if (workPage) {
        setupExam(workPage);
    }
});
