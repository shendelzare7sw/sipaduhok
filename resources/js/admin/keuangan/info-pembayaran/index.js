const setPanelState = (type) => {
    const viewPanel = document.getElementById(`${type}-view`);
    const editPanel = document.getElementById(`${type}-edit`);

    if (!viewPanel || !editPanel) {
        return;
    }

    const shouldShowEdit = !editPanel.classList.contains('is-visible');

    viewPanel.classList.toggle('is-hidden', shouldShowEdit);
    editPanel.classList.toggle('is-visible', shouldShowEdit);
};

const updateModeDisplay = () => {
    const checkbox = document.getElementById('is_production');
    const modeLabel = document.getElementById('modeLabel');
    const modeDescription = document.getElementById('modeDescription');
    const modeCard = document.getElementById('modeSelectionCard');

    if (!checkbox || !modeLabel || !modeDescription || !modeCard) {
        return;
    }

    if (checkbox.checked) {
        modeLabel.innerHTML = '<span class="text-danger"><i class="fas fa-broadcast-tower me-1"></i> Mode Production</span>';
        modeDescription.textContent = 'Transaksi nyata dengan uang sungguhan.';
        modeCard.classList.add('is-production');
        modeCard.classList.remove('is-sandbox');
        return;
    }

    modeLabel.innerHTML = '<span class="text-warning"><i class="fas fa-vial me-1"></i> Mode Sandbox</span>';
    modeDescription.textContent = 'Simulasi pembayaran untuk testing.';
    modeCard.classList.add('is-sandbox');
    modeCard.classList.remove('is-production');
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-edit]').forEach((button) => {
        button.addEventListener('click', () => {
            setPanelState(button.dataset.type);
        });
    });

    document.querySelectorAll('[data-submit-on-change]').forEach((input) => {
        input.addEventListener('change', () => {
            input.form?.submit();
        });
    });

    document.getElementById('is_production')?.addEventListener('change', updateModeDisplay);
    updateModeDisplay();
});
