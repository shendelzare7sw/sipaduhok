function updateRadio(input) {
    const container = input.closest('.pilihan-list');
    if (!container) {
        return;
    }

    container.querySelectorAll('.pilihan-item').forEach((item) => item.classList.remove('selected'));
    input.closest('.pilihan-item')?.classList.add('selected');
}

function updateCheckbox(input) {
    const item = input.closest('.pilihan-item');
    if (!item) {
        return;
    }

    item.classList.toggle('selected', input.checked);
}

function updateBSOption(input) {
    const row = input.closest('tr');
    if (!row) {
        return;
    }

    row.querySelectorAll('.bs-option').forEach((option) => option.classList.remove('selected'));
    input.closest('.bs-option')?.classList.add('selected');
}

function updateEssayCounter(textarea) {
    const soal = textarea.closest('.soal-uraian');
    if (!soal) {
        return;
    }

    const charCounter = soal.querySelector('[data-char-counter]');
    const wordCounter = soal.querySelector('[data-word-counter]');
    const text = textarea.value;

    if (charCounter) {
        charCounter.textContent = text.length;
    }

    if (wordCounter) {
        wordCounter.textContent = text.trim() ? text.trim().split(/\s+/).length : 0;
    }
}

document.addEventListener('change', (event) => {
    const target = event.target;

    if (target.matches('.soal-pilgan input[type="radio"]')) {
        updateRadio(target);
        return;
    }

    if (target.matches('.soal-pilgan-kompleks input[type="checkbox"]')) {
        updateCheckbox(target);
        return;
    }

    if (target.matches('.soal-benar-salah input[type="radio"]')) {
        updateBSOption(target);
    }
});

document.addEventListener('input', (event) => {
    if (event.target.matches('.uraian-textarea')) {
        updateEssayCounter(event.target);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.uraian-textarea').forEach(updateEssayCounter);
});
