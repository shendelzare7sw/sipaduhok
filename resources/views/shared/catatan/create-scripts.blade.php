@php
    $recipientMode = $recipientMode ?? 'multi';
@endphp

<script>
function togglePenerimaFields() {
    const tipe = document.querySelector('input[name="tipe_penerima"]:checked')?.value;
    const roleField = document.getElementById('roleField');
    const individuField = document.getElementById('individuField');

    roleField?.classList.add('catatan-hidden');
    individuField?.classList.add('catatan-hidden');

    if (tipe === 'role') {
        roleField?.classList.remove('catatan-hidden');
    }

    if (tipe === 'individu') {
        individuField?.classList.remove('catatan-hidden');
        if (typeof renderUserList === 'function') {
            renderUserList();
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    togglePenerimaFields();
});
</script>

@if($recipientMode === 'multi')
<script>
const allUsers = @json($usersForIndividu ?? []);
const selectedUserIds = new Set(@json(collect(old('penerima_ids', []))->map(fn($id) => (string) $id)->values()));

function escapeHtml(value) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(value ?? ''));
    return div.innerHTML;
}

function filteredUsers() {
    const role = document.getElementById('filterRole')?.value ?? '';
    const cabang = document.getElementById('filterCabang')?.value ?? '';
    const search = (document.getElementById('filterSearch')?.value ?? '').toLowerCase().trim();

    return allUsers.filter((user) => {
        const matchRole = !role || user.role === role;
        const matchCabang = !cabang || String(user.cabang_id ?? '') === cabang;
        const matchSearch = !search || String(user.name ?? '').toLowerCase().includes(search);
        return matchRole && matchCabang && matchSearch;
    });
}

function renderUserList() {
    const list = document.getElementById('userChecklist');
    if (!list) return;

    const users = filteredUsers();
    list.innerHTML = '';

    if (users.length === 0) {
        list.innerHTML = '<div class="catatan-empty py-4"><i class="fas fa-search"></i><h5>Tidak ada penerima</h5><p class="mb-0">Coba ubah filter atau kata pencarian.</p></div>';
        updateSelectedCount();
        syncHiddenSelectedInputs();
        return;
    }

    users.forEach((user) => {
        const id = String(user.id);
        const item = document.createElement('label');
        item.className = 'catatan-user-item';
        item.innerHTML = `
            <input type="checkbox" value="${escapeHtml(id)}" ${selectedUserIds.has(id) ? 'checked' : ''}>
            <span>
                <span class="catatan-user-name">
                    ${escapeHtml(user.name)}
                    <span class="catatan-mini-badge">${escapeHtml(user.role_label)}</span>
                </span>
                <span class="catatan-user-meta">${escapeHtml(user.cabang_name || '-')}</span>
            </span>
        `;

        const checkbox = item.querySelector('input');
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                selectedUserIds.add(id);
            } else {
                selectedUserIds.delete(id);
            }
            updateSelectedCount();
            syncHiddenSelectedInputs();
        });

        list.appendChild(item);
    });

    updateSelectedCount();
    syncHiddenSelectedInputs();
}

function syncHiddenSelectedInputs() {
    document.querySelectorAll('input[data-generated-penerima="true"]').forEach((input) => input.remove());

    selectedUserIds.forEach((id) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'penerima_ids[]';
        input.value = id;
        input.dataset.generatedPenerima = 'true';
        document.querySelector('form')?.appendChild(input);
    });
}

function updateSelectedCount() {
    const count = selectedUserIds.size;
    const counter = document.getElementById('selectedCount');
    if (counter) {
        counter.textContent = count + ' dipilih';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    ['filterRole', 'filterCabang', 'filterSearch'].forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener(id === 'filterSearch' ? 'input' : 'change', renderUserList);
    });

    document.getElementById('selectAllBtn')?.addEventListener('click', function () {
        filteredUsers().forEach((user) => selectedUserIds.add(String(user.id)));
        renderUserList();
    });

    document.getElementById('clearAllBtn')?.addEventListener('click', function () {
        selectedUserIds.clear();
        renderUserList();
    });

    syncHiddenSelectedInputs();
    updateSelectedCount();
});
</script>
@endif
