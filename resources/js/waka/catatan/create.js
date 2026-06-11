/* Catatan create behavior extracted from the former shared loader. */
const readCatatanCreateConfig = () => {
    const configEl = document.getElementById('catatanCreateConfig');

    if (!configEl?.dataset.config) {
        return {
            recipientMode: 'multi',
            users: [],
            selectedUserIds: [],
        };
    }

    try {
        return JSON.parse(configEl.dataset.config);
    } catch (error) {
        console.error('Gagal membaca konfigurasi catatan.', error);
        return {
            recipientMode: 'multi',
            users: [],
            selectedUserIds: [],
        };
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const config = readCatatanCreateConfig();
    const form = document.querySelector('[data-catatan-create-form]');
    const roleField = document.getElementById('roleField');
    const individuField = document.getElementById('individuField');
    const userChecklist = document.getElementById('userChecklist');
    const selectedUserIds = new Set((config.selectedUserIds || []).map((id) => String(id)));
    const allUsers = Array.isArray(config.users) ? config.users : [];

    const updateSelectedCount = () => {
        const counter = document.getElementById('selectedCount');

        if (counter) {
            counter.textContent = `${selectedUserIds.size} dipilih`;
        }
    };

    const syncHiddenSelectedInputs = () => {
        if (!form) {
            return;
        }

        form.querySelectorAll('input[data-generated-penerima="true"]').forEach((input) => input.remove());

        selectedUserIds.forEach((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'penerima_ids[]';
            input.value = id;
            input.dataset.generatedPenerima = 'true';
            form.appendChild(input);
        });
    };

    const filteredUsers = () => {
        const role = document.getElementById('filterRole')?.value ?? '';
        const cabang = document.getElementById('filterCabang')?.value ?? '';
        const search = (document.getElementById('filterSearch')?.value ?? '').toLowerCase().trim();

        return allUsers.filter((user) => {
            const matchRole = !role || user.role === role;
            const matchCabang = !cabang || String(user.cabang_id ?? '') === cabang;
            const matchSearch = !search || String(user.name ?? '').toLowerCase().includes(search);

            return matchRole && matchCabang && matchSearch;
        });
    };

    const renderEmptyUsers = () => {
        if (!userChecklist) {
            return;
        }

        userChecklist.innerHTML = `
            <div class="catatan-empty py-4">
                <i class="fas fa-search"></i>
                <h5>Tidak ada penerima</h5>
                <p class="mb-0">Coba ubah filter atau kata pencarian.</p>
            </div>
        `;
    };

    const createUserItem = (user) => {
        const id = String(user.id);
        const item = document.createElement('label');
        const checkbox = document.createElement('input');
        const body = document.createElement('span');
        const name = document.createElement('span');
        const badge = document.createElement('span');
        const meta = document.createElement('span');

        item.className = 'catatan-user-item';
        checkbox.type = 'checkbox';
        checkbox.value = id;
        checkbox.checked = selectedUserIds.has(id);

        name.className = 'catatan-user-name';
        name.append(document.createTextNode(user.name ?? ''));

        badge.className = 'catatan-mini-badge';
        badge.textContent = user.role_label ?? '';
        name.appendChild(badge);

        meta.className = 'catatan-user-meta';
        meta.textContent = user.cabang_name || '-';

        body.append(name, meta);
        item.append(checkbox, body);

        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                selectedUserIds.add(id);
            } else {
                selectedUserIds.delete(id);
            }

            updateSelectedCount();
            syncHiddenSelectedInputs();
        });

        return item;
    };

    const renderUserList = () => {
        if (!userChecklist) {
            return;
        }

        const users = filteredUsers();
        userChecklist.innerHTML = '';

        if (users.length === 0) {
            renderEmptyUsers();
            updateSelectedCount();
            syncHiddenSelectedInputs();
            return;
        }

        users.forEach((user) => {
            userChecklist.appendChild(createUserItem(user));
        });

        updateSelectedCount();
        syncHiddenSelectedInputs();
    };

    const togglePenerimaFields = () => {
        const tipe = document.querySelector('input[name="tipe_penerima"]:checked')?.value;

        roleField?.classList.add('catatan-hidden');
        individuField?.classList.add('catatan-hidden');

        if (tipe === 'role') {
            roleField?.classList.remove('catatan-hidden');
        }

        if (tipe === 'individu') {
            individuField?.classList.remove('catatan-hidden');
            renderUserList();
        }
    };

    document.querySelectorAll('[data-catatan-recipient-option]').forEach((input) => {
        input.addEventListener('change', togglePenerimaFields);
    });

    ['filterRole', 'filterCabang', 'filterSearch'].forEach((id) => {
        const el = document.getElementById(id);

        if (!el) {
            return;
        }

        el.addEventListener(id === 'filterSearch' ? 'input' : 'change', renderUserList);
    });

    document.getElementById('selectAllBtn')?.addEventListener('click', () => {
        filteredUsers().forEach((user) => selectedUserIds.add(String(user.id)));
        renderUserList();
    });

    document.getElementById('clearAllBtn')?.addEventListener('click', () => {
        selectedUserIds.clear();
        renderUserList();
    });

    togglePenerimaFields();
    syncHiddenSelectedInputs();
    updateSelectedCount();
});
