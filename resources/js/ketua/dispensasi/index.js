const getSelectedIds = () => Array.from(document.querySelectorAll('.disp-checkbox:checked')).map((checkbox) => checkbox.value);

const setModalTitle = (label) => {
    const title = document.getElementById('catatanModalTitle');

    if (!title) {
        return;
    }

    title.replaceChildren();

    const icon = document.createElement('i');
    icon.className = 'fas fa-question-circle me-2';
    title.appendChild(icon);
    title.append(label);
};

const setSubmitButton = (label, buttonClass) => {
    const submitButton = document.getElementById('catatanSubmitBtn');

    if (!submitButton) {
        return;
    }

    submitButton.className = `btn fw-bold ${buttonClass}`;
    submitButton.replaceChildren();

    const icon = document.createElement('i');
    icon.className = 'fas fa-check me-1';
    submitButton.appendChild(icon);
    submitButton.append(` ${label}`);
};

const setupModal = (action, ids, message, label, buttonClass) => {
    const form = document.getElementById('catatanForm');
    const messageTarget = document.getElementById('catatanModalMessage');
    const header = document.getElementById('catatanModalHeader');
    const container = document.getElementById('catatanBulkIds');
    const modalElement = document.getElementById('catatanModal');

    if (!form || !messageTarget || !header || !container || !modalElement) {
        return;
    }

    form.action = action;
    setModalTitle(label);
    messageTarget.textContent = message;
    setSubmitButton(label, buttonClass);

    header.className = `modal-header ${buttonClass.includes('success') ? 'bg-success text-white' : 'bg-danger text-white'}`;
    container.replaceChildren();

    ids.forEach((id) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'dispensasi_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    new bootstrap.Modal(modalElement).show();
};

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('select-all')?.addEventListener('change', (event) => {
        document.querySelectorAll('.disp-checkbox').forEach((checkbox) => {
            checkbox.checked = event.target.checked;
        });
    });

    document.querySelectorAll('[data-single-action]').forEach((button) => {
        button.addEventListener('click', () => {
            setupModal(
                button.dataset.action,
                [button.dataset.id],
                button.dataset.message,
                button.dataset.label,
                button.dataset.buttonClass,
            );
        });
    });

    document.querySelectorAll('[data-bulk-action]').forEach((button) => {
        button.addEventListener('click', () => {
            const ids = getSelectedIds();

            if (ids.length === 0) {
                const warningModal = document.getElementById('peringatanModal');

                if (warningModal) {
                    new bootstrap.Modal(warningModal).show();
                }

                return;
            }

            setupModal(
                button.dataset.action,
                ids,
                `${button.dataset.label} untuk ${ids.length} pengajuan terpilih?`,
                button.dataset.label,
                button.dataset.buttonClass,
            );
        });
    });
});
