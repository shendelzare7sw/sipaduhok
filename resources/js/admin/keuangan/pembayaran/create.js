const parseCurrency = (value) => parseInt(String(value).replace(/\D/g, ''), 10) || 0;

const formatNumber = (value) => String(value).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

const formatAndValidateInput = (input) => {
    const value = parseCurrency(input.value);
    const max = parseInt(input.dataset.max, 10) || 0;

    input.value = formatNumber(value);
    input.classList.toggle('is-invalid', value > max);
    input.classList.toggle('text-danger', value > max);

    const checkbox = document.querySelector(`input[name="tagihan_ids[]"][value="${input.dataset.id}"]`);
    if (value > 0 && checkbox && !checkbox.checked) {
        checkbox.checked = true;
    }
};

const calculateTotal = () => {
    let total = 0;
    let count = 0;
    let hasError = false;

    document.querySelectorAll('.nominal-input').forEach((input) => {
        const checkbox = document.querySelector(`input[name="tagihan_ids[]"][value="${input.dataset.id}"]`);

        if (input.classList.contains('is-invalid') && checkbox?.checked) {
            hasError = true;
        }
    });

    document.querySelectorAll('.tagihan-checkbox:checked').forEach((checkbox) => {
        const inputNominal = document.getElementById(`nominal-${checkbox.dataset.id}`);

        if (inputNominal) {
            total += parseCurrency(inputNominal.value);
            count += 1;
        }
    });

    const jumlahBayar = document.getElementById('jumlah_bayar');
    const jumlahBayarDisplay = document.getElementById('jumlah_bayar_display');
    const countSelected = document.getElementById('count-selected');
    const totalDisplay = document.getElementById('total-display');
    const submitButton = document.querySelector('[data-submit-payment]');

    if (jumlahBayar) {
        jumlahBayar.value = total;
    }

    if (jumlahBayarDisplay) {
        jumlahBayarDisplay.value = `Rp ${formatNumber(total)}`;
    }

    if (countSelected) {
        countSelected.textContent = `${count} item`;
    }

    if (totalDisplay) {
        totalDisplay.textContent = `Rp ${formatNumber(total)}`;
    }

    if (submitButton) {
        submitButton.disabled = !(count > 0 && total > 0 && !hasError);
    }

    return { count, total, hasError };
};

const prepareNominalInputs = () => {
    document.querySelectorAll('.nominal-input').forEach((input) => {
        input.value = String(parseCurrency(input.value));
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nominal-input').forEach((input) => {
        input.addEventListener('input', () => {
            formatAndValidateInput(input);
            calculateTotal();
        });
    });

    document.querySelectorAll('.tagihan-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', calculateTotal);
    });

    document.querySelector('[data-payment-form]')?.addEventListener('submit', (event) => {
        const summary = calculateTotal();

        if (summary.count === 0 || summary.total <= 0 || summary.hasError) {
            event.preventDefault();
            return;
        }

        prepareNominalInputs();
    });

    calculateTotal();
});
